<?php


namespace Debuqer\EloquentMemory\Transitions;


class ModelRestored extends ModelUpdated implements TransitionInterface
{
    public function getRollbackChange(): TransitionInterface
    {
        return new ModelSoftDeleted([
            'model_class' => $this->getModelClass(),
            'key' => $this->getModelKey(),
            'old' => $this->getAttributes(),
            'attributes' => $this->getOldAttributes()
        ]);
    }
}
