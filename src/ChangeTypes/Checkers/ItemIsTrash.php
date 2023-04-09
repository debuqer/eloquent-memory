<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Checkers;


use Illuminate\Database\Eloquent\SoftDeletes;

class ItemIsTrash extends AbstractChecker
{
    public function condition(): bool
    {
        return (ItemIsModel::define($this->item)->evaluate() and class_uses($this->item, SoftDeletes::class) and $this->item->trashed());
    }
}
