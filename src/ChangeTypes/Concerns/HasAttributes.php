<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Concerns;


trait HasAttributes
{
    protected $attributes;

    public function getAttributes()
    {
        return $this->attributes;
    }
}
