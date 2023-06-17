<?php


namespace Debuqer\EloquentMemory\StorageModels\Concerns;

trait CanGenerateBatchId
{
    protected static $batch;

    public static function getBatchId(): string
    {
        if ( ! static::$batch ) {
            static::$batch = md5(microtime(true));
        }

        return static::$batch;
    }
}
