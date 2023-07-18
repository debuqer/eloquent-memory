<?php

namespace Debuqer\EloquentMemory\Transitions\Concerns;

trait HasProperties
{
    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }
}
