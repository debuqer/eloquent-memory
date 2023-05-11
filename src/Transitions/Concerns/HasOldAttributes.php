<?php


namespace Debuqer\EloquentMemory\Transitions\Concerns;


trait HasOldAttributes
{
    public function getOldAttributes()
    {
        return isset($this->properties['old']) ? $this->properties['old'] : null;
    }

    public function setOldAttributes(array $beforeAttributes)
    {
        $this->properties['old'] = $beforeAttributes;
    }
}
