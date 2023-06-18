<?php


namespace Debuqer\EloquentMemory\Repositories;


use Debuqer\EloquentMemory\Timeline;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;

interface TransitionPersistDriverInterface
{
    public static function persist(TransitionInterface $transition);
    public static function find(array $where): Timeline;
}
