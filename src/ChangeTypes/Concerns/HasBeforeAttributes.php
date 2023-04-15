<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Concerns;


trait HasBeforeAttributes
{
    protected $before;

    public function getBeforeAttributes()
    {
        return $this->before;
    }
}
