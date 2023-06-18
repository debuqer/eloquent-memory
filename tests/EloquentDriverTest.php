<?php
use Debuqer\EloquentMemory\Facades\EloquentMemory;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithEloquentMemory;
use Debuqer\EloquentMemory\Timeline;
use Debuqer\EloquentMemory\Repositories\TransitionRepository;
use Debuqer\EloquentMemory\Repositories\PersistedTransactionRecordInterface;
use Carbon\Carbon;

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

it('can query on transitions', function () {
    $now = Carbon::now();
    for ( $i = 0; $i <= 9; $i ++ ) {
        Carbon::setTestNow((clone $now)->subMinutes($i));
        $transition = $this->getTransition('model-created', PostWithEloquentMemory::class);
        $transition['handler']->persist();
    }

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     * |    |    | -9 | -8 | -7 | -6 | -5 | -4 | -3 | -2 | -1 |  0 |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */



    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    | *  | *  | *  | *  | *  | *  | *  | *  | *  |  * |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionRepository::class)->find([])->count();
    expect($count)->toBe(10);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    | -9 | -8 | -7 | -6 | -5 | -4 | -3 | -2 | -1 |  0 |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionRepository::class)->find(['after' => $now])->count();
    expect($count)->toBe(0);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    | -9 | -8 | -7 | -6 | -5 | *  | *  | *  | *  |  * |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionRepository::class)->find(['after' => (clone $now)->subMinutes(5)])->count();
    expect($count)->toBe(5);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    | -9 | -8 | -7 | -6 |  * | *  | *  | *  | *  |  * |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionRepository::class)->find(['from' => (clone $now)->subMinutes(5)])->count();
    expect($count)->toBe(6);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    |  * |  * |  * |  * | -5 | -4 | -3 | -2 | -1 |  0 |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionRepository::class)->find(['before' => (clone $now)->subMinutes(5)])->count();
    expect($count)->toBe(4);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    |  * |  * |  * |  * |  * | -4 | -3 | -2 | -1 |  0 |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionRepository::class)->find(['until' => (clone $now)->subMinutes(5)])->count();
    expect($count)->toBe(5);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    | -9 | -8 | * | * | * | -4 | -3 | -2 | -1 |  0 |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionRepository::class)->find(['until' => (clone $now)->subMinutes(5), 'after' => (clone $now)->subMinutes(8)])->count();
    expect($count)->toBe(3);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    | -9 | -8 | -7 | -6 | -5 |  * |  * | -2 | -1 |  0 |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionRepository::class)->find(['before' => (clone $now)->subMinutes(2), 'from' => (clone $now)->subMinutes(4)])->count();
    expect($count)->toBe(2);
});
