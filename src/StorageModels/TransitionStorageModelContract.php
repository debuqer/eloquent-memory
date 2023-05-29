<?php


namespace Debuqer\EloquentMemory\StorageModels;


use Debuqer\EloquentMemory\Timeline;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;

interface TransitionStorageModelContract
{
    public static function persist(TransitionInterface $transition);
    public static function findUsingBatch($batch);
    public static function getBatchId(): string;
    public static function find(array $where): Timeline;
    public function getTransition(): TransitionInterface;
}
