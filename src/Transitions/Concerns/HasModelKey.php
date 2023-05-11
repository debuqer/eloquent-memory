<?php


namespace Debuqer\EloquentMemory\Transitions\Concerns;


trait HasModelKey
{
    public function getModelKey()
    {
        return isset($this->properties['key']) ? $this->properties['key'] : null;
    }

    public function setModelKey($key)
    {
        $this->properties['key'] = $key;
    }
}
