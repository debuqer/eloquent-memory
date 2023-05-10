<?php


namespace Debuqer\EloquentMemory\Models;


use Debuqer\EloquentMemory\Transitions\TransitionInterface;

interface ModelTransitionInterface
{
    public static function persist(TransitionInterface $transition);
    public static function findUsingBatch($batch);
}
