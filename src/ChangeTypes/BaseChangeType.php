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

    /**
     * @codeCoverageIgnore
     * @return mixed
     */
    public function down()
    {
        return $this->getRollbackChange()->apply();
    }
}
