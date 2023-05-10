<?php


namespace Debuqer\EloquentMemory\Transitions\Concerns;


trait HasAttributes
{
    use HasParameters;


    public function getAttributes()
    {
        return isset($this->parameters['attributes']) ? $this->parameters['attributes'] : null;
    }

    public function setAttributes(array $attributes)
    {
        $this->parameters['attributes'] = $attributes;
    }
}
