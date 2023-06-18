<?php
use Debuqer\EloquentMemory\Facades\EloquentMemory;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithEloquentMemory;
use Debuqer\EloquentMemory\Timeline;
use Debuqer\EloquentMemory\Repositories\TransitionRepository;
use Debuqer\EloquentMemory\Repositories\PersistedTransactionRecordInterface;

it('can persist normal transition', function () {
    $batchId = EloquentMemory::batchId();
    $transition = $this->getTransition('model-created', PostWithEloquentMemory::class);
    $transition['handler']->persist();

    /** @var Timeline $timeline */
    $timeline = app(TransitionRepository::class)->find(['batch' => $batchId]);

    /** @var PersistedTransactionRecordInterface $current */
    $current = $timeline->current();

    expect($current->getTransition()->getType())->toBe('model-created');
    expect($current->getTransition()->getSubjectType())->toBe(get_class($transition['model']));
    expect($current->getTransition()->getSubjectKey())->toBe($transition['model']->getKey());
});
