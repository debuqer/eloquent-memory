<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Concerns;


trait HasParameters
{
    protected $parameters = [];

    public function getParameters()
    {
        return $this->parameters;
    }
}
