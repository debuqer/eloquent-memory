<?php


namespace Debuqer\EloquentMemory\Models;


use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Debuqer\EloquentMemory\Timeline;

interface ModelTransitionInterface
{
    public static function persist(TransitionInterface $transition);
    public static function findUsingBatch($batch);
    public static function getBatchId(): string;
    public static function find(array $where): Timeline;
    public function getTransition(): TransitionInterface;
}
