<?php

namespace Debuqer\EloquentMemory\Transitions;

use Illuminate\Database\Eloquent\Model;

class ModelCreated extends BaseTransition implements TransitionInterface
{
    public const TypeName = 'model-created';

    /**
     * @return mixed
     */
    public function getModelCreatedFromState(Model $current)
    {
        return $current
            ->setRawAttributes($this->getProperties()['attributes'])
            ->setExists(true)
            ->syncChanges();
    }
}
