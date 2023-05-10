<?php


namespace Debuqer\EloquentMemory\Transitions\Concerns;


trait HasParameters
{
    protected $parameters = [];

    public function getParameters()
    {
        return $this->parameters;
    }
}