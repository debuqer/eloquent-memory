<?php


namespace Debuqer\EloquentMemory\Models\Concerns;


use Illuminate\Support\Str;

trait CanGenerateBatchId
{
    protected static $batch;

    public static function getBatchId(): string
    {
        if ( ! static::$batch ) {
            static::$batch = Str::orderedUuid();
        }

        return static::$batch;
    }
}
