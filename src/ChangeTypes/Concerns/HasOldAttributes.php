<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Concerns;


trait HasOldAttributes
{
    use HasParameters;

    public function getOldAttributes()
    {
        return isset($this->parameters['old']) ? $this->parameters['old'] : null;
    }

    public function setBeforeAttributes(array $beforeAttributes)
    {
        $this->parameters['old'] = $beforeAttributes;
    }
}
