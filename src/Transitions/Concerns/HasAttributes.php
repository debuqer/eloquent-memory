<?php


namespace Debuqer\EloquentMemory\Transitions\Concerns;


trait HasAttributes
{
    /**
     * @return mixed|null
     */
    public function getAttributes()
    {
        return isset($this->properties['attributes']) ? $this->properties['attributes'] : null;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->properties['attributes'] = $attributes;
    }
}
