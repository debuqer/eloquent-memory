<?php


namespace Debuqer\EloquentMemory\ChangeTypes;

use Illuminate\Support\Str;

abstract class BaseChangeType
{
    abstract function up();
    abstract function getRollbackChange();

    public function getType(): string
    {
        return Str::kebab($this->getClassName());
    }

    protected function getClassName()
    {
        return explode('\\', get_class($this))[count(explode('\\', get_class($this)))-1];
    }

    /**
     * @codeCoverageIgnore
     * @return mixed
     */
    public function down()
    {
        return $this->getRollbackChange()->up();
    }
}
