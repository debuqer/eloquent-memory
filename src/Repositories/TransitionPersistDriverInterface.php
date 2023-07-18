<?php

namespace Debuqer\EloquentMemory\Repositories;

use Carbon\Carbon;
use Debuqer\EloquentMemory\Timeline;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;

interface TransitionPersistDriverInterface
{
    /**
     * @param TransitionInterface $transition
     * @param Carbon $dateRecorded
     */
    public static function persist(TransitionInterface $transition, Carbon $dateRecorded): void;

    /**
     * @param array $where
     * @return Timeline
     */
    public static function find(array $where): Timeline;
}
