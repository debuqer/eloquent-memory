<?php
use Debuqer\EloquentMemory\Timeline;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithEloquentMemory;
use Debuqer\EloquentMemory\StorageModels\TransitionRepository;
use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Facades\EloquentMemory;

it('can create a model from persisted ModelCreated', function () {
    $batchId = EloquentMemory::batchId();

    $model = $this->createAModelOf(PostWithEloquentMemory::class);
    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $batchId]);
    $current = $timeline->current();

    $transitionCreatedFromPersistedRecord = EloquentMemory::getTransitionFromPersistedRecord($current);
    expect($transitionCreatedFromPersistedRecord)->toBeInstanceOf(ModelCreated::class);
    expect($transitionCreatedFromPersistedRecord->getSubject())->toBeInstanceOf(get_class($model));
});

it('can create a ModelCreated from model', function () {
    $model = $this->createAModelOf(PostWithEloquentMemory::class);

    $transitionCreatedFromModel = EloquentMemory::getTransitionFromModel('model-created', $model);
    expect($transitionCreatedFromModel)->toBeInstanceOf(ModelCreated::class);
    expect($transitionCreatedFromModel->getSubject())->toBeInstanceOf(get_class($model));
});

it('can persist ModelCreated', function () {
    $batchId = EloquentMemory::batchId();
    $model = $this->createAModelOf(PostWithEloquentMemory::class);

    $transitionCreatedFromModel = EloquentMemory::getTransitionFromModel('model-created', $model);
    $transitionCreatedFromModel->persist();

    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $batchId]);
    $current = $timeline->current();

    expect($current)->not->toBeNull();
});

it('can create a model from persisted ModelUpdated', function () {
    $batchId = EloquentMemory::batchId();

    $model = $this->createAModelOf(PostWithEloquentMemory::class);
    $model->update([
        'title' => 'new Title'
    ]);
    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $batchId]);
    $current = $timeline->current();

    $transitionCreatedFromPersistedRecord = EloquentMemory::getTransitionFromPersistedRecord($current);
    expect($transitionCreatedFromPersistedRecord)->toBeInstanceOf(ModelUpdated::class);
    expect($transitionCreatedFromPersistedRecord->getSubject())->toBeInstanceOf(get_class($model));
});

it('can create a ModelUpdated from model', function () {
    $model = $this->createAModelOf(PostWithEloquentMemory::class);
    $model->update([
        'title' => 'new Title',
    ]);

    $transitionCreatedFromModel = EloquentMemory::getTransitionFromModel('model-updated', $model);
    expect($transitionCreatedFromModel)->toBeInstanceOf(ModelUpdated::class);
    expect($transitionCreatedFromModel->getSubject())->toBeInstanceOf(get_class($model));
});

it('can persist ModelUpdated', function () {
    $batchId = EloquentMemory::batchId();
    $model = $this->createAModelOf(PostWithEloquentMemory::class);
    $model->update([
        'title' => 'new Title',
    ]);

    $transitionCreatedFromModel = EloquentMemory::getTransitionFromModel('model-updated', $model);
    $transitionCreatedFromModel->persist();

    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $batchId]);
    $current = $timeline->current();

    expect($current)->not->toBeNull();
});

it('can create a model from persisted ModelDeleted', function () {
    $batchId = EloquentMemory::batchId();

    $model = $this->createAModelOf(PostWithEloquentMemory::class);
    $model->delete();
    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $batchId]);
    $current = $timeline->current();

    $transitionCreatedFromPersistedRecord = EloquentMemory::getTransitionFromPersistedRecord($current);
    expect($transitionCreatedFromPersistedRecord)->toBeInstanceOf(ModelDeleted::class);
});

it('can create a ModelDeleted from model', function () {
    $model = $this->createAModelOf(PostWithEloquentMemory::class);
    $model->delete();

    $transitionCreatedFromModel = EloquentMemory::getTransitionFromModel('model-deleted', $model);
    expect($transitionCreatedFromModel)->toBeInstanceOf(ModelDeleted::class);
    expect($transitionCreatedFromModel->getSubject())->toBeInstanceOf(get_class($model));
});

it('can persist ModelDeleted', function () {
    $batchId = EloquentMemory::batchId();
    $model = $this->createAModelOf(PostWithEloquentMemory::class);
    $model->delete();

    $transitionCreatedFromModel = EloquentMemory::getTransitionFromModel('model-deleted', $model);
    $transitionCreatedFromModel->persist();

    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $batchId]);
    $current = $timeline->current();

    expect($current)->not->toBeNull();
});

