<?php
use Debuqer\EloquentMemory\Repositories\TransitionRepository;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithEloquentMemory;
use Debuqer\EloquentMemory\Tests\Fixtures\SoftDeletedPostWithEloquentMemory;
use Debuqer\EloquentMemory\Timeline;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithExcludeAttributes;

beforeEach(function () {
    $this->batch_id = \Debuqer\EloquentMemory\Facades\EloquentMemory::batchId();
});

it('can record when a model created', function () {
    $model = $this->createAModelOf(PostWithEloquentMemory::class);
    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $this->batch_id]);
    $current = $timeline->current();

    expect($timeline->count())->toBe(1);
    expect($current->getTransition()->getType())->toBe('model-created');
    expect($current->getTransition()->getSubjectType())->toBe(PostWithEloquentMemory::class);
    expect($this->arraysAreTheSame($current->getTransition()->getAttributes(), $model->getAttributes()));
});

it('can record when a model deleted', function () {
    $model = $this->createAModelOf(PostWithEloquentMemory::class);
    $oldAttributes = $model->getRawOriginal();

    $model->delete();
    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $this->batch_id]);
    $current = $timeline->current();

    expect($timeline->count())->toBe(2);
    expect($current->getTransition()->getType())->toBe('model-deleted');
    expect($current->getTransition()->getSubjectType())->toBe(PostWithEloquentMemory::class);
    expect($this->arraysAreTheSame($current->getTransition()->getAttributes(), $oldAttributes));
});


it('it can record when model soft deleted', function () {
    $model = $this->createAModelOf(SoftDeletedPostWithEloquentMemory::class);
    $oldAttributes = $model->getRawOriginal();

    $model->delete();
    $attributes = $model->getRawOriginal();

    $timeline = app(TransitionRepository::class)->find(['batch' => $this->batch_id]);

    $current = $timeline->current();
    expect($current->getTransition()->getType())->toBe('model-updated');
    expect($current->getTransition()->getSubjectType())->toBe(SoftDeletedPostWithEloquentMemory::class);
    expect($current->getTransition()->getSubjectKey())->toBe($model->id);
    expect($this->arraysAreTheSame($current->getTransition()->getAttributes(), $oldAttributes))->toBeTrue();
    $timeline->next();
    $current = $timeline->current();
    expect($current->getTransition()->getType())->toBe('model-created');
});


it('can record when model restored', function () {
    $model = $this->createAModelOf(SoftDeletedPostWithEloquentMemory::class);
    $model->delete();
    $oldAttributes = $model->getRawOriginal();
    $model->restore();
    $attributes = $model->getRawOriginal();

    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $this->batch_id]);

    $current = $timeline->current();
    expect($current->getTransition()->getType())->toBe('model-updated');
    expect($current->getTransition()->getSubjectType())->toBe(SoftDeletedPostWithEloquentMemory::class);
    expect($current->getTransition()->getSubjectKey())->toBe($model->id);
    expect($current->getTransition()->getAttributes())->toBe($attributes);

    $timeline->next();
    $current = $timeline->current();
    expect($current->getTransition()->getType())->toBe('model-updated');

    $timeline->next();
    $current = $timeline->current();
    expect($current->getTransition()->getType())->toBe('model-created');
});


it('can record when a model updated', function () {
    $model = $this->createAModelOf(PostWithEloquentMemory::class);
    $oldAttributes = $model->getRawOriginal();


    $model->update([
        'title' => 'new Title'
    ]);
    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $this->batch_id]);
    $current = $timeline->current();

    expect($timeline->count())->toBe(2);
    expect($current->getTransition()->getType())->toBe('model-updated');
    expect($current->getTransition()->getSubjectType())->toBe(PostWithEloquentMemory::class);
    expect($current->getTransition()->getSubjectKey())->toBe($model->id);
    expect($this->arraysAreTheSame($current->getTransition()->getAttributes(), $model->getRawOriginal()))->toBeTrue();
});


it('can record chain of events if soft delete support', function() {
    $expectedStack = [];

    $model = $this->createAModelOf(SoftDeletedPostWithEloquentMemory::class);
    $expectedStack[] = 'model-created';

    $model->update([
        'title' => 'new Title'
    ]);
    $expectedStack[] = 'model-updated';

    $model->update([
        'title' => 'new new Title'
    ]);
    $expectedStack[] = 'model-updated';


    $model->delete();
    $expectedStack[] = 'model-updated';

    $model->restore();
    $expectedStack[] = 'model-updated';

    $model->update([
        'title' => 'new Title'
    ]);
    $expectedStack[] = 'model-updated';

    $model->forceDelete();
    $expectedStack[] = 'model-deleted';

    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $this->batch_id]);

    foreach (array_reverse($expectedStack) as $expected) {
        expect($timeline->current()->getTransition()->getType())->toBe($expected);
        $timeline->next();
    }
});

it('can exclude attributes', function() {
    $model = $this->createAModelOf(PostWithExcludeAttributes::class);
    $model->update([
        'title' => 'changed',
    ]);
    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $this->batch_id]);
    $current = $timeline->current();

    $attributes = $current->getTransition()->getAttributes();
    expect(isset($attributes['title']))->toBeFalse();
});
