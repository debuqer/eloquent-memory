<?php


namespace Debuqer\EloquentMemory\ChangeTypes;

use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsNotTrash;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsTrash;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemsAreTheSame;

class ModelSoftDeleted extends ModelUpdated implements ChangeTypeInterface
{

    public static function isApplicable($old, $new): bool
    {
        return (
            ItemIsTrash::setItem($new)->evaluate() and
            ItemIsNotTrash::setItem($old)->evaluate() and
            ItemsAreTheSame::setItem($old)->setExpect($new)->evaluate()
        );
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelRestored($this->getModelClass(), $this->getAfterAttributes(), $this->getBeforeAttributes());
    }
}
