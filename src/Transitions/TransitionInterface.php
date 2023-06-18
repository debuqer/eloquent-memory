<?php


namespace Debuqer\EloquentMemory\Transitions;


use Debuqer\EloquentMemory\Repositories\TransitionPersistDriverInterface;
use Debuqer\EloquentMemory\Repositories\PersistedTransactionRecordInterface;
use Illuminate\Database\Eloquent\Model;

interface TransitionInterface
{
    public static function createFromPersistedRecord(PersistedTransactionRecordInterface $change);

    public function getProperties(): array;
    public function getType(): string;

    public function getSubject();
    public function getSubjectType();
    public function getSubjectKey();
    public function getTransitionStorageAddress(): string;

    public function persist();
    public function getModelCreatedFromState();
}
