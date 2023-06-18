<?php


namespace Debuqer\EloquentMemory\Repositories;

use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Illuminate\Support\Collection;

interface PersistedTransactionRecordInterface
{
    /**
     * @return TransitionInterface
     */
    public function getTransition(): TransitionInterface;

    /**
     * @param array $data
     * @return Collection
     */
    public static function queryOnTransitions(array $data): Collection;
}
