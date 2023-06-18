<?php


namespace Debuqer\EloquentMemory\Repositories;

use Debuqer\EloquentMemory\Transitions\TransitionInterface;

interface ModelInterface
{
    public function getTransition(): TransitionInterface;
    public static function queryOnTransitions(array $data);
}
