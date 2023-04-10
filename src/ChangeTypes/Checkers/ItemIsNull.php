<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Checkers;


class ItemIsNull extends AbstractChecker
{
    public function condition(): bool
    {
        return (! $this->item );
    }
}
