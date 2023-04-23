<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


class ModelRestored extends ModelUpdated implements ChangeTypeInterface
{
    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelSoftDeleted($this->getModelClass(), $this->getModelKey(), $this->getAttributes(), $this->getOldAttributes());
    }
}
