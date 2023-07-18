<?php

namespace Debuqer\EloquentMemory\Transitions;

use Debuqer\EloquentMemory\Transitions\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;

class ModelDeleted extends BaseTransition implements TransitionInterface
{
    public const TypeName = "model-deleted";

    /**
     * @param Model $model
     * @return BaseTransition
     */
    public static function createFromModel(Model $model)
    {
        /** @var BaseTransition $transition */
        $transition = new static([
            'attributes' => static::getMemorizableAttributes($model)
        ]);
        $transition->setSubject($model);

        return $transition;
    }

    /**
     * @param Model $current
     * @return null
     */
    public function getModelCreatedFromState(Model $current)
    {
        return null;
    }
}
