<?php

namespace Debuqer\EloquentMemory;

use Debuqer\EloquentMemory\StorageModels\TransitionStorageModelContract;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Illuminate\Database\Eloquent\Model;

class EloquentMemory
{
    public function getTransitionFromModel(string $type, Model $model)
    {
        /** @var TransitionInterface $transitionClass */
        $transitionClass = config('eloquent-memory.changes.'.$type);

        return $transitionClass::createFromModel($model);
    }

    public function getTransitionFromPersistedRecord(TransitionStorageModelContract $record)
    {
        return $record->getTransition();
    }
}
