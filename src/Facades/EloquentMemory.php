<?php

namespace Debuqer\EloquentMemory\Facades;

use Debuqer\EloquentMemory\Repositories\TransitionPersistDriverInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Debuqer\EloquentMemory\EloquentMemory
 *
 * @method getTransitionFromModel(string $type, Model $model)
 * @method getTransitionFromPersistedRecord(TransitionPersistDriverInterface $record)
 * @method batchId()
 */
class EloquentMemory extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Debuqer\EloquentMemory\EloquentMemory::class;
    }
}
