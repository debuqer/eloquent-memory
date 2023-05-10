<?php


namespace Debuqer\EloquentMemory\Transitions;


class ModelSoftDeleted extends ModelUpdated implements TransitionInterface
{
    public function getRollbackChange(): TransitionInterface
    {
        return new ModelRestored($this->getModelClass(), $this->getModelKey(), $this->getAttributes(), $this->getOldAttributes());
    }
}
