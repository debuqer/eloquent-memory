<?php


namespace Debuqer\EloquentMemory\Transitions\Concerns;


trait HasModelClass
{
    use HasParameters;

    public function getModelClass()
    {
        return isset($this->parameters['model_class']) ? $this->parameters['model_class'] : null;
    }

    public function setModelClass(string $modelClass)
    {
        $this->parameters['model_class'] = $modelClass;
    }

    protected function getModelInstance($parameters = [])
    {
        return app($this->getModelClass(), $parameters);
    }
}
