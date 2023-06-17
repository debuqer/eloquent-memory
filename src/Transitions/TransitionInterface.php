<?php


namespace Debuqer\EloquentMemory\Transitions;


use Debuqer\EloquentMemory\StorageModels\TransitionStorageModelContract;
use Illuminate\Database\Eloquent\Model;

interface TransitionInterface
{
    public function getProperties(): array;
    public function getType(): string;
    public function getSubjectKey();
    public function getSubject();
    public function getSubjectType();

    public function persist();
    public function getModel();

    public static function createFromPersistedRecord(TransitionStorageModelContract $change);
    public function getTransitionStorageAddress(): string;

    public function getModelCreatedFromState();
}
