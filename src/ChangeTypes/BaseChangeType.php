<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Illuminate\Database\Eloquent\Model;

abstract class BaseChangeType
{
    /** @var Model */
    protected $model;

    public function getType(): string
    {
        return static::TYPE;
    }

    public function rollback()
    {
        return $this->getRollbackChange()->apply();
    }
}
