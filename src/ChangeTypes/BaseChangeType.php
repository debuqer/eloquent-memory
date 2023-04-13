<?php


namespace Debuqer\EloquentMemory\ChangeTypes;

abstract class BaseChangeType
{
    abstract function up();
    abstract function getRollbackChange();

    public function getType(): string
    {
        return static::TYPE;
    }

    public function down()
    {
        return $this->getRollbackChange()->apply();
    }
}
