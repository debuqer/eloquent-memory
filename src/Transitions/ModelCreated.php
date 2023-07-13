<?php

namespace Debuqer\EloquentMemory\Transitions;

use Debuqer\EloquentMemory\Transitions\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;

class ModelCreated extends BaseTransition implements TransitionInterface
{
    public const TypeName = "model-created";

    /**
     * @param Model $model
     * @return TransitionInterface
     */
    public static function createFromModel(Model $model): TransitionInterface
    {
        /** @var BaseTransition $transition */
        $transition = new static(['attributes' => static::getMemorizableAttributes($model)]);
        $transition->setSubject($model);

        return $transition;
    }

    /**
     * @param Model $current
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
