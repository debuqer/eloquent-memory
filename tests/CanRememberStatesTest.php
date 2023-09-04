<?php

use Debuqer\EloquentMemory\Repositories\TransitionQuery;
use Debuqer\EloquentMemory\Repositories\TransitionPersistDriver;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithExcludeAttributes;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithRememberState;
use Debuqer\EloquentMemory\Tests\Fixtures\SoftDeletedPostWithRememberState;
use Debuqer\EloquentMemory\Timeline;

beforeEach(function () {
    $this->batch_id = \Debuqer\EloquentMemory\Facades\EloquentMemory::batchId();
});

it('can record when a model created', function () {
    $expectedStack = [];

    $model = $this->createAModelOf(PostWithRememberState::class);
    $expectedStack[] = 'user-created';
    $expectedStack[] = 'model-created';
    /** @var Timeline $timeline */
    $timeline = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setBatch($this->batch_id));
    $current = $timeline->current();

    expect($timeline->count())->toBe(count($expectedStack));
    expect($current->getTransition()->getType())->toBe('model-created');
    expect($current->getTransition()->getSubjectType())->toBe(PostWithRememberState::class);
    expect($this->arraysAreTheSame($current->getTransition()->getAttributes(), $model->getAttributes()));
});

it('can record when a model deleted', function () {
    $expectedStack = [];

    $model = $this->createAModelOf(PostWithRememberState::class);
    $expectedStack[] = 'model-created';
    $expectedStack[] = 'user-created';
    $oldAttributes = $model->getRawOriginal();

    $model->delete();
    $expectedStack[] = 'model-deleted';

    /** @var Timeline $timeline */
    $timeline = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setBatch($this->batch_id));
    $current = $timeline->current();

    expect($timeline->count())->toBe(count($expectedStack));
    expect($current->getTransition()->getType())->toBe('model-deleted');
    expect($current->getTransition()->getSubjectType())->toBe(PostWithRememberState::class);
    expect($this->arraysAreTheSame($current->getTransition()->getAttributes(), $oldAttributes));
});

it('it can record when model soft deleted', function () {
    $model = $this->createAModelOf(SoftDeletedPostWithRememberState::class);
    $oldAttributes = $model->getRawOriginal();

    $model->delete();
    $attributes = $model->getRawOriginal();

    $timeline = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setBatch($this->batch_id));

    $current = $timeline->current();
    expect($current->getTransition()->getType())->toBe('model-updated');
    expect($current->getTransition()->getSubjectType())->toBe(SoftDeletedPostWithRememberState::class);
    expect($current->getTransition()->getSubjectKey())->toBe($model->id);
    expect($this->arraysAreTheSame($current->getTransition()->getAttributes(), $oldAttributes))->toBeTrue();
    $timeline->next();
    $current = $timeline->current();
    expect($current->getTransition()->getType())->toBe('model-created');
});

it('can record when model restored', function () {
    $expectedStack = [];
    $model = $this->createAModelOf(SoftDeletedPostWithRememberState::class);
    $expectedStack[] = 'model-created'; // user created
    $expectedStack[] = 'model-created'; // post created

    $model->delete();
    $expectedStack[] = 'model-updated'; // seted deleted_at
    $model->restore();
    $expectedStack[] = 'model-updated';
    $attributes = $model->getRawOriginal(); // unseted deleted_at

    /** @var Timeline $timeline */
    $timeline = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setBatch($this->batch_id));

    $current = $timeline->current();

    expect($current->getTransition()->getSubjectType())->toBe(SoftDeletedPostWithRememberState::class);
    expect($current->getTransition()->getSubjectKey())->toBe($model->id);
    expect($current->getTransition()->getAttributes())->toBe($attributes);
    foreach (array_reverse($expectedStack) as $i => $expected) {
        expect($current->getTransition()->getType())->toBe($expected);
        $timeline->next();
        $current = $timeline->current();
    }
});

it('can record when a model updated', function () {
    $model = $this->createAModelOf(PostWithRememberState::class);
    $expectedStack = [];
    $expectedStack[] = 'model-created';
    $expectedStack[] = 'user-created';

    $model->update([
        'title' => 'new Title',
    ]);
    $expectedStack[] = 'model-updated';

    /** @var Timeline $timeline */
    $timeline = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setBatch($this->batch_id));
    $current = $timeline->current();

    expect($timeline->count())->toBe(count($expectedStack));
    expect($current->getTransition()->getType())->toBe('model-updated');
    expect($current->getTransition()->getSubjectType())->toBe(PostWithRememberState::class);
    expect($current->getTransition()->getSubjectKey())->toBe($model->id);
    expect($this->arraysAreTheSame($current->getTransition()->getAttributes(), $model->getRawOriginal()))->toBeTrue();
});

it('can record chain of events if soft delete support', function () {
    $expectedStack = [];

    $model = $this->createAModelOf(SoftDeletedPostWithRememberState::class);
    $expectedStack[] = 'model-created';  // user
    $expectedStack[] = 'model-created';

    $model->update([
        'title' => 'new Title',
    ]);
    $expectedStack[] = 'model-updated';

    $model->update([
        'title' => 'new new Title',
    ]);
    $expectedStack[] = 'model-updated';

    $model->delete();
    $expectedStack[] = 'model-updated';

    $model->restore();
    $expectedStack[] = 'model-updated';

    $model->update([
        'title' => 'new Title',
    ]);
    $expectedStack[] = 'model-updated';

    $model->forceDelete();
    $expectedStack[] = 'model-deleted';

    /** @var Timeline $timeline */
    $timeline = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setBatch($this->batch_id));

    foreach (array_reverse($expectedStack) as $expected) {
        expect($timeline->current()->getTransition()->getType())->toBe($expected);
        $timeline->next();
    }
});

it('can exclude attributes', function () {
    $model = $this->createAModelOf(PostWithExcludeAttributes::class);
    $model->update([
        'title' => 'changed',
    ]);
    /** @var Timeline $timeline */
    $timeline = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setBatch($this->batch_id));
    $current = $timeline->current();

    $attributes = $current->getTransition()->getAttributes();
    expect(isset($attributes['title']))->toBeFalse();
});
