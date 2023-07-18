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
}
