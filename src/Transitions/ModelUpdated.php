<?php

namespace Debuqer\EloquentMemory\Transitions;

use Illuminate\Database\Eloquent\Model;

class ModelUpdated extends BaseTransition implements TransitionInterface
{
    public const TypeName = 'model-updated';

    /**
     * @return Model
     */
    public function getModelCreatedFromState(Model $current)
    {
        return $current
            ->setRawAttributes($this->getProperties()['attributes'])
            ->setExists(true)
            ->syncChanges();
    }
}
