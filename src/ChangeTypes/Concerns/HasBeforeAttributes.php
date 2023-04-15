<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Concerns;


trait HasBeforeAttributes
{
    use HasParameters;

    public function getBeforeAttributes()
    {
        return isset($this->parameters['before']) ? $this->parameters['before'] : null;
    }

    public function setBeforeAttributes(array $beforeAttributes)
    {
        $this->parameters['before'] = $beforeAttributes;
    }
}
