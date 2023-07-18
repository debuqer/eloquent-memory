<?php

namespace Debuqer\EloquentMemory\Transitions;

use Illuminate\Database\Eloquent\Model;

class ModelCreated extends BaseTransition implements TransitionInterface
{
    public const TypeName = 'model-created';

    public static function createFromModel(Model $model): TransitionInterface
    {
        /** @var BaseTransition $transition */
        $transition = new static(['attributes' => static::getMemorizableAttributes($model)]);
        $transition->setSubject($model);

        return $transition;
    }

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
