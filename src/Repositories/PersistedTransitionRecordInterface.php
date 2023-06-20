<?php


namespace Debuqer\EloquentMemory\Repositories;

use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Illuminate\Support\Collection;

interface PersistedTransitionRecordInterface
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

    public function getProperties(): array;
    public function getSubjectType(): string;
    public function getSubjectKey(): string;
}
