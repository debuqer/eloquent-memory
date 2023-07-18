<?php

namespace Debuqer\EloquentMemory\Transitions\Concerns;

trait HasProperties
{
    /**
     * @var array
     */
    protected $properties = [];

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties($properties)
    {
        $this->properties = $properties;
    }
}
