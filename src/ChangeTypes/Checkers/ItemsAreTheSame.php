<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Checkers;



class ItemsAreTheSame extends ItemsAreTheSameType
{
    public function condition(): bool
    {
        return $this->item->getKey() === $this->expect->getKey();
    }
}
