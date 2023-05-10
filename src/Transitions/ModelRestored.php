<?php


namespace Debuqer\EloquentMemory\Transitions;


class ModelRestored extends ModelUpdated implements TransitionInterface
{
    public function getRollbackChange(): TransitionInterface
    {
        return new ModelSoftDeleted($this->getModelClass(), $this->getModelKey(), $this->getAttributes(), $this->getOldAttributes());
    }
}
