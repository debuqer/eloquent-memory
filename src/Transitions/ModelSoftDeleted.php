<?php


namespace Debuqer\EloquentMemory\Transitions;


class ModelSoftDeleted extends ModelUpdated implements TransitionInterface
{
    public function getRollbackChange(): TransitionInterface
    {
        return new ModelRestored([
            'model_class' => $this->getModelClass(),
            'key' => $this->getModelKey(),
            'old' => $this->getAttributes(),
            'attributes' => $this->getOldAttributes()
        ]);
    }
}
