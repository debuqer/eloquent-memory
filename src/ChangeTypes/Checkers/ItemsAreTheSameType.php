<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Checkers;


class ItemsAreTheSameType extends AbstractChecker
{
    protected $expect;

    public function setExpect($expect)
    {
        $this->expect = $expect;

        return $this;
    }

    public function condition(): bool
    {
        return (get_class($this->item) === get_class($this->expect));
    }
}
