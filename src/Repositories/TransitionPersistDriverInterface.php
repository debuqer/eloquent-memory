<?php


namespace Debuqer\EloquentMemory\Repositories;


use Debuqer\EloquentMemory\Timeline;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;

interface TransitionPersistDriverInterface
{
    /**
     * @param TransitionInterface $transition
     */
    public static function persist(TransitionInterface $transition): void;

    /**
     * @param array $where
     * @return Timeline
     */
    public static function find(array $where): Timeline;
}
