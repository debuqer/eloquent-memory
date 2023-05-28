<?php
use Debuqer\EloquentMemory\Models\TransitionRepository;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithEloquentMemory;
use Debuqer\EloquentMemory\Tests\Fixtures\SoftDeletedPostWithEloquentMemory;
use Debuqer\EloquentMemory\Timeline;

beforeEach(function () {
    $this->batch_id = app(TransitionRepository::class)->getBatchId();
});

it('can record when a model created', function () {
    $model = $this->createAModelOf(PostWithEloquentMemory::class);
    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $this->batch_id]);
    $current = $timeline->current();

    expect($timeline->count())->toBe(1);
    expect($current->getTransition()->getType())->toBe('model-created');
    expect($current->getTransition()->getModelClass())->toBe(PostWithEloquentMemory::class);
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
    expect($current->getTransition()->getModelClass())->toBe(PostWithEloquentMemory::class);
    expect($this->arraysAreTheSame($current->getTransition()->getOldAttributes(), $oldAttributes));
});


it('it can record when model soft deleted', function () {
    $model = $this->createAModelOf(SoftDeletedPostWithEloquentMemory::class);
    $oldAttributes = [$model->getDeletedAtColumn() => $model->getAttribute($model->getDeletedAtColumn())];
    $model->delete();
    $attributes = [$model->getDeletedAtColumn() => $model->getAttributeValue($model->getDeletedAtColumn())->format('Y-m-d\TH:i:s.u\Z')];

    $timeline = app(TransitionRepository::class)->find(['batch' => $this->batch_id]);

    $current = $timeline->current();
    expect($current->getTransition()->getType())->toBe('model-soft-deleted');
    expect($current->getTransition()->getModelClass())->toBe(SoftDeletedPostWithEloquentMemory::class);
    expect($current->getTransition()->getModelKey())->toBe($model->id);
    expect($current->getTransition()->getOldAttributes())->toBe($oldAttributes);
    expect($current->getTransition()->getAttributes())->toBe($attributes);
    $timeline->next();
    $current = $timeline->current();
    expect($current->getTransition()->getType())->toBe('model-created');
});


test('it can record when model restored', function () {
    $model = $this->createAPost(SoftDeletedPostWithEloquentMemory::class);
    $model->delete();
    $oldAttributes = [$model->getDeletedAtColumn() => $model->getAttributeValue($model->getDeletedAtColumn())->format('Y-m-d H:i:s')];
    $model->restore();
    $attributes = [$model->getDeletedAtColumn() => $model->getAttribute($model->getDeletedAtColumn())];

    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $this->batch_id]);

    $current = $timeline->current();
    expect($current->getTransition()->getType())->toBe('model-restored');
    expect($current->getTransition()->getModelClass())->toBe(SoftDeletedPostWithEloquentMemory::class);
    expect($current->getTransition()->getModelKey())->toBe($model->id);
    expect($current->getTransition()->getOldAttributes())->toBe($oldAttributes);
    expect($current->getTransition()->getAttributes())->toBe($attributes);
    $timeline->next();
    $current = $timeline->current();
    expect($current->getTransition()->getType())->toBe('model-soft-deleted');
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
    expect($current->getTransition()->getModelClass())->toBe(PostWithEloquentMemory::class);
    expect($current->getTransition()->getModelKey())->toBe($model->id);
    expect($current->getTransition()->getOldAttributes())->toBe($oldAttributes);
    expect($current->getTransition()->getAttributes())->toBe($model->getRawOriginal());
});
