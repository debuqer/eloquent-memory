<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Checkers;


class ItemExists extends ItemIsModel
{
    public function condition(): bool
    {
        return (parent::condition() and $this->item->exists);
    }
}
