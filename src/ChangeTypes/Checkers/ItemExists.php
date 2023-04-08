<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Checkers;


class ItemExists extends AbstractChecker
{
    public function condition(): bool
    {
        return ($this->item->exists);
    }
}
