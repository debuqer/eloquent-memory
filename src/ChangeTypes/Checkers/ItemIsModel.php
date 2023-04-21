<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Checkers;


use Illuminate\Database\Eloquent\Model;

class ItemIsModel extends AbstractChecker
{
    public function condition(): bool
    {
        return (is_a($this->item, Model::class));
    }
}
