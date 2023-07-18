<?php

namespace Debuqer\EloquentMemory\Repositories;

use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Illuminate\Support\Collection;

interface PersistedTransitionRecordInterface
{
    public function getTransition(): TransitionInterface;

    public static function queryOnTransitions(TransitionQuery $data): Collection;

    public function getType(): string;

    public function getProperties(): array;

    public function getSubjectType(): string;

    public function getSubjectKey(): string;

    public function getCreationDate(): string;
}
