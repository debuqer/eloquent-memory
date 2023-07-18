<?php

namespace Debuqer\EloquentMemory\Transitions;

use Illuminate\Database\Eloquent\Model;

class ModelDeleted extends BaseTransition implements TransitionInterface
{
    public const TypeName = 'model-deleted';

    /**
     * @return BaseTransition
     */
    public static function createFromModel(Model $model)
    {
        /** @var BaseTransition $transition */
        $transition = new static([
            'attributes' => static::getMemorizableAttributes($model),
        ]);
        $transition->setSubject($model);

        return $transition;
    }

    /**
     * @return null
     */
    public function getModelCreatedFromState(Model $current)
    {
        return null;
    }
}
