<?php


namespace Debuqer\EloquentMemory\Transitions\Concerns;


trait HasProperties
{
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
