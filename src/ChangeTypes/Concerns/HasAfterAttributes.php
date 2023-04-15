<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Concerns;


trait HasAfterAttributes
{
    use HasParameters;

    public function getAfterAttributes()
    {
        return isset($this->parameters['after']) ? $this->parameters['after'] : null;
    }

    public function setAfterAttributes(array $afterAttributes)
    {
        $this->parameters['after'] = $afterAttributes;
    }
}
