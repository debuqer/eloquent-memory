<?php

namespace Debuqer\EloquentMemory\Transitions;

use Illuminate\Database\Eloquent\Model;

class ModelDeleted extends BaseTransition implements TransitionInterface
{
    public const TypeName = 'model-deleted';

    /**
     * @return null
     */
    public function getModelCreatedFromState(Model $current)
    {
        return null;
    }
}
