<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Concerns;


trait HasAfterAttributes
{
    protected $after;

    public function getAfterAttributes()
    {
        return $this->after;
    }
}
