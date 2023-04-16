<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Concerns;


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
