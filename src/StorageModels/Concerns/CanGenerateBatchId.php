<?php


namespace Debuqer\EloquentMemory\StorageModels\Concerns;


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
