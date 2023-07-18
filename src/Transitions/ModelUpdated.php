<?php

namespace Debuqer\EloquentMemory\Transitions;

use Illuminate\Database\Eloquent\Model;

class ModelUpdated extends BaseTransition implements TransitionInterface
{
    public const TypeName = 'model-updated';

    /**
     * @return static
     */
    public static function createFromModel(Model $model)
    {
        $transition = new static(['attributes' => static::getMemorizableAttributes($model)]);
        $transition->setSubject($model);

        return $transition;
    }

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
