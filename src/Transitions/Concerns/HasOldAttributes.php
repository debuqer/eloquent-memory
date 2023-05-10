<?php


namespace Debuqer\EloquentMemory\Transitions\Concerns;


trait HasOldAttributes
{
    use HasParameters;

    public function getOldAttributes()
    {
        return isset($this->parameters['old']) ? $this->parameters['old'] : null;
    }

    public function setOldAttributes(array $beforeAttributes)
    {
        $this->parameters['old'] = $beforeAttributes;
    }
}