<?php

namespace Debuqer\EloquentMemory;

use Illuminate\Support\Str;

class EloquentMemory
{
    protected static $batch;

    public function getBatch(): string
    {
        if ( ! static::$batch ) {
            static::$batch = Str::orderedUuid();
        }

        return static::$batch;
    }
}
