<?php


namespace Debuqer\EloquentMemory\Transitions\Concerns;


trait HasAttributes
{
    public function getAttributes()
    {
        return isset($this->properties['attributes']) ? $this->properties['attributes'] : null;
    }

    public function setAttributes(array $attributes)
    {
        $this->properties['attributes'] = $attributes;
    }
}
