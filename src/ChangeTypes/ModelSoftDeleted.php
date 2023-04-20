<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


class ModelSoftDeleted extends ModelUpdated implements ChangeTypeInterface
{
    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelRestored($this->getModelClass(), $this->getAttributes(), $this->getBeforeAttributes());
    }
}
