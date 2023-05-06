<?php

namespace Debuqer\EloquentMemory\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Debuqer\EloquentMemory\EloquentMemory
 *
 * @method getBatch()
 *
 */
class EloquentMemory extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Debuqer\EloquentMemory\EloquentMemory::class;
    }
}
