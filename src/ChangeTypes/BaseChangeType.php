<?php


namespace Debuqer\EloquentMemory\ChangeTypes;

abstract class BaseChangeType
{
    abstract function apply();
    abstract function getRollbackChange();

    public function getType(): string
    {
        return static::TYPE;
    }

    public function rollback()
    {
        return $this->getRollbackChange()->apply();
    }
}
