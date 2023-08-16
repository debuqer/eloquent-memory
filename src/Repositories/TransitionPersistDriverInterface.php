<?php

namespace Debuqer\EloquentMemory\Repositories;

use Carbon\Carbon;
use Debuqer\EloquentMemory\Timeline;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;

interface TransitionPersistDriverInterface
{
    public static function persist(TransitionInterface $transition, Carbon $dateRecorded): void;

    public static function find(TransitionQuery $where): Timeline;
}
