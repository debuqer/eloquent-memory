<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Checkers;


use Illuminate\Database\Eloquent\SoftDeletes;

class ItemUseSoftDelete extends ItemIsModel
{
    public function condition(): bool
    {
        return (parent::condition() and class_uses($this->item, SoftDeletes::class) and ! $this->item->isForceDeleting() );
    }
}
