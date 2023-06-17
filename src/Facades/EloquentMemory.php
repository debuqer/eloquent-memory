<?php

namespace Debuqer\EloquentMemory\Facades;

use Debuqer\EloquentMemory\StorageModels\TransitionStorageModelContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Debuqer\EloquentMemory\EloquentMemory
 *
 * @method getTransitionFromModel(string $type, Model $model)
 * @method getTransitionFromPersistedRecord(TransitionStorageModelContract $record)
 */
class EloquentMemory extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Debuqer\EloquentMemory\EloquentMemory::class;
    }
}
