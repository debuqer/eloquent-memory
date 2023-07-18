<?php

use Debuqer\EloquentMemory\Facades\EloquentMemory;
use Debuqer\EloquentMemory\Repositories\PersistedTransitionRecordInterface;
use Debuqer\EloquentMemory\Repositories\TransitionRepository;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithRememberState;
use Debuqer\EloquentMemory\Timeline;
use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;

it('can create a model from persisted ModelCreated', function () {
    $batchId = EloquentMemory::batchId();

    $model = $this->createAModelOf(PostWithRememberState::class);
    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $batchId]);
    $current = $timeline->current();

    $transitionCreatedFromPersistedRecord = EloquentMemory::getTransitionFromPersistedRecord($current);
    expect($transitionCreatedFromPersistedRecord)->toBeInstanceOf(ModelCreated::class);
    expect($transitionCreatedFromPersistedRecord->getSubject())->toBeInstanceOf(get_class($model));
});

it('can create a ModelCreated from model', function () {
    $model = $this->createAModelOf(PostWithRememberState::class);

    $transitionCreatedFromModel = EloquentMemory::getTransitionFromModel('model-created', $model);
    expect($transitionCreatedFromModel)->toBeInstanceOf(ModelCreated::class);
    expect($transitionCreatedFromModel->getSubject())->toBeInstanceOf(get_class($model));
});

it('can persist ModelCreated', function () {
    $batchId = EloquentMemory::batchId();
    $model = $this->createAModelOf(PostWithRememberState::class);

    $transitionCreatedFromModel = EloquentMemory::getTransitionFromModel('model-created', $model);
    $transitionCreatedFromModel->persist();

    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $batchId]);
    $current = $timeline->current();

    expect($current)->not->toBeNull();
});

it('can get the model from state of ModelCreated', function () {
    /** @var TransitionInterface $transition */
    $transition = $this->getTransition('model-created', PostWithRememberState::class);

    expect($this->arraysAreTheSame($transition['handler']->getModelCreatedFromState($transition['model'])->getRawOriginal(), $transition['model']->getRawOriginal()))->toBeTrue();
});

it('can create a model from persisted ModelUpdated', function () {
    $batchId = EloquentMemory::batchId();

    $model = $this->createAModelOf(PostWithRememberState::class);
    $model->update([
        'title' => 'new Title',
    ]);
    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $batchId]);
    $current = $timeline->current();

    $transitionCreatedFromPersistedRecord = EloquentMemory::getTransitionFromPersistedRecord($current);
    expect($transitionCreatedFromPersistedRecord)->toBeInstanceOf(ModelUpdated::class);
    expect($transitionCreatedFromPersistedRecord->getSubject())->toBeInstanceOf(get_class($model));
});

it('can create a ModelUpdated from model', function () {
    $model = $this->createAModelOf(PostWithRememberState::class);
    $model->update([
        'title' => 'new Title',
    ]);

    $transitionCreatedFromModel = EloquentMemory::getTransitionFromModel('model-updated', $model);
    expect($transitionCreatedFromModel)->toBeInstanceOf(ModelUpdated::class);
    expect($transitionCreatedFromModel->getSubject())->toBeInstanceOf(get_class($model));
});

it('can persist ModelUpdated', function () {
    $batchId = EloquentMemory::batchId();
    $model = $this->createAModelOf(PostWithRememberState::class);
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

it('can get the model from state of ModelUpdated', function () {
    /** @var TransitionInterface $transition */
    $transition = $this->getTransition('model-updated', PostWithRememberState::class);

    expect($this->arraysAreTheSame($transition['handler']->getModelCreatedFromState($transition['model'])->getRawOriginal(), $transition['model']->getRawOriginal()))->toBeTrue();
});

it('can create a model from persisted ModelDeleted', function () {
    $batchId = EloquentMemory::batchId();

    $model = $this->createAModelOf(PostWithRememberState::class);
    $model->delete();
    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $batchId]);
    $current = $timeline->current();

    $transitionCreatedFromPersistedRecord = EloquentMemory::getTransitionFromPersistedRecord($current);
    expect($transitionCreatedFromPersistedRecord)->toBeInstanceOf(ModelDeleted::class);
});

it('can create a ModelDeleted from model', function () {
    $model = $this->createAModelOf(PostWithRememberState::class);
    $model->delete();

    $transitionCreatedFromModel = EloquentMemory::getTransitionFromModel('model-deleted', $model);
    expect($transitionCreatedFromModel)->toBeInstanceOf(ModelDeleted::class);
    expect($transitionCreatedFromModel->getSubject())->toBeInstanceOf(get_class($model));
});

it('can persist ModelDeleted', function () {
    $batchId = EloquentMemory::batchId();
    $model = $this->createAModelOf(PostWithRememberState::class);
    $model->delete();

    $transitionCreatedFromModel = EloquentMemory::getTransitionFromModel('model-deleted', $model);
    $transitionCreatedFromModel->persist();

    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $batchId]);
    $current = $timeline->current();

    expect($current)->not->toBeNull();
});

it('can get the model from state of ModelDeleted', function () {
    /** @var TransitionInterface $transition */
    $transition = $this->getTransition('model-deleted', PostWithRememberState::class);

    expect($transition['handler']->getModelCreatedFromState($transition['model']))->toBeNull();
});

it('has unique address', function () {
    $batchId = EloquentMemory::batchId();
    $model = $this->createAModelOf(PostWithRememberState::class);

    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $batchId]);
    /** @var PersistedTransitionRecordInterface $current */
    $current = $timeline->current();

    expect($current->getTransition()->getTransitionStorageAddress())->toBe($model->getModelAddress());
});
