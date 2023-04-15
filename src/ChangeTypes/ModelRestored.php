<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsNotTrash;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsTrash;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemsAreTheSame;

class ModelRestored extends ModelUpdated implements ChangeTypeInterface
{
    public static function isApplicable($old, $new): bool
    {
        return (
            ItemIsTrash::setItem($old)->evaluate() and
            ItemIsNotTrash::setItem($new)->evaluate() and
            ItemsAreTheSame::setItem($old)->setExpect($new)->evaluate()
        );
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelSoftDeleted($this->getModelClass(), $this->getAfterAttributes(), $this->getBeforeAttributes());
    }
}
