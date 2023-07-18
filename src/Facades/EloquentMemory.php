<?php

namespace Debuqer\EloquentMemory\Facades;

use Debuqer\EloquentMemory\Repositories\TransitionPersistDriverInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use \Debuqer\EloquentMemory\EloquentMemory as EM;

/**
 * @see EM
 *
 * @method static getTransitionFromModel(string $type, Model $model)
 * @method static getTransitionFromPersistedRecord(TransitionPersistDriverInterface $record)
 * @method static batchId()
 */
class EloquentMemory extends Facade
{
    protected static function getFacadeAccessor()
    {
        return EM::class;
    }
}
