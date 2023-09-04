<?php

use Debuqer\EloquentMemory\Facades\EloquentMemory;
use Debuqer\EloquentMemory\Repositories\PersistedTransitionRecordInterface;
use Debuqer\EloquentMemory\Repositories\TransitionQuery;
use Debuqer\EloquentMemory\Repositories\TransitionPersistDriver;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithRememberState;
use Debuqer\EloquentMemory\Timeline;

it('can persist normal transition', function () {
    $batchId = EloquentMemory::batchId();
    $transition = $this->getTransition('model-created', PostWithRememberState::class);
    $transition['handler']->persist();

    /** @var Timeline $timeline */
    $timeline = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setBatch($batchId));

    /** @var PersistedTransitionRecordInterface $current */
    $current = $timeline->current();

    expect($current->getTransition()->getType())->toBe('model-created');
    expect($current->getTransition()->getSubjectType())->toBe(get_class($transition['model']));
    expect($current->getTransition()->getSubjectKey())->toBe($transition['model']->getKey());
});

it('can query on transitions', function () {
    $now = app('time')->now();
    for ($i = 0; $i <= 9; $i++) {
        app('time')->setTestNow((clone $now)->subMinutes($i));
        $transition = $this->getTransition('model-created', PostWithRememberState::class);
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
    $count = app(TransitionPersistDriver::class)->find(TransitionQuery::create())->count();
    expect($count)->toBe(10);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    | -9 | -8 | -7 | -6 | -5 | -4 | -3 | -2 | -1 |  0 |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setAfter($now))->count();
    expect($count)->toBe(0);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    | -9 | -8 | -7 | -6 | -5 | *  | *  | *  | *  |  * |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setAfter((clone $now)->subMinutes(5)))->count();
    expect($count)->toBe(5);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    | -9 | -8 | -7 | -6 |  * | *  | *  | *  | *  |  * |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setFrom((clone $now)->subMinutes(5)))->count();
    expect($count)->toBe(6);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    |  * |  * |  * |  * | -5 | -4 | -3 | -2 | -1 |  0 |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setBefore((clone $now)->subMinutes(5)))->count();
    expect($count)->toBe(4);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    |  * |  * |  * |  * |  * | -4 | -3 | -2 | -1 |  0 |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setUntil((clone $now)->subMinutes(5)))->count();
    expect($count)->toBe(5);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    | -9 | -8 | * | * | * | -4 | -3 | -2 | -1 |  0 |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setUntil((clone $now)->subMinutes(5))->setAfter((clone $now)->subMinutes(8)))->count();
    expect($count)->toBe(3);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    | -9 | -8 | -7 | -6 | -5 |  * |  * | -2 | -1 |  0 |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setBefore((clone $now)->subMinutes(2))->setFrom((clone $now)->subMinutes(4)))->count();
    expect($count)->toBe(2);

    /**
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     *  |    |    | -9 | -8 | * | * | * | -4 | -3 | -2 | -1 |  0 |
     *  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
     */
    $count = app(TransitionPersistDriver::class)->find(TransitionQuery::create()->setUntil((clone $now)->subMinutes(5))->setAfter((clone $now)->subMinutes(8))->setTake(1))->count();
    expect($count)->toBe(1);
});
