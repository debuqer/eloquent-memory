<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Checkers;


use Illuminate\Database\Eloquent\SoftDeletes;

class ItemIsTrash extends ItemUseSoftDelete
{
    public function condition(): bool
    {
        return (parent::condition() and $this->item->trashed());
    }
}
