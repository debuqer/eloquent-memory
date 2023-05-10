<?php


namespace Debuqer\EloquentMemory\Transitions;

use Debuqer\EloquentMemory\Facades\EloquentMemory;
use Debuqer\EloquentMemory\Models\ModelTransition;
use Illuminate\Support\Str;

abstract class BaseTransition implements TransitionInterface
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
        $this->model = ModelTransition::create([
            'type' => $this->getType(),
            'parameters' => $this->getParameters(),
            'batch' => EloquentMemory::getBatch()
        ]);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }
}