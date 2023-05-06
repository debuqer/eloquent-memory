<?php


namespace Debuqer\EloquentMemory\ChangeTypes;

use Debuqer\EloquentMemory\Change;
use Debuqer\EloquentMemory\Facades\EloquentMemory;
use Illuminate\Support\Str;

abstract class BaseChangeType implements ChangeTypeInterface
{
    protected $model;

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

    public function persist()
    {
        $this->model = Change::create([
            'type' => $this->getType(),
            'parameters' => $this->getParameters(),
            'batch' => EloquentMemory::getBatch()
        ]);
    }

    public function getModel()
    {
        return $this->model;
    }
}
