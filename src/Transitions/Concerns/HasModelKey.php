<?php


namespace Debuqer\EloquentMemory\Transitions\Concerns;


trait HasModelKey
{
    use HasParameters;

    public function getModelKey()
    {
        return isset($this->parameters['key']) ? $this->parameters['key'] : null;
    }

    public function setModelKey($key)
    {
        $this->parameters['key'] = $key;
    }
}