<?php


namespace Debuqer\EloquentMemory\Transitions\Concerns;


trait HasModelClass
{
    public function getModelClass()
    {
        return $this->properties['model_class'] ?? null;
    }

    public function setModelClass(string $modelClass)
    {
        $this->properties['model_class'] = $modelClass;
    }

    protected function getModelObject($properties = [])
    {
        return app($this->getModelClass(), $properties);
    }
}
