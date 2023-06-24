<?php

namespace Debuqer\EloquentMemory\Transitions;

use Debuqer\EloquentMemory\Transitions\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;

class ModelUpdated extends BaseTransition implements TransitionInterface
{
    public const TypeName = "model-updated";

    /**
     * @param Model $model
     * @return static
     */
    public static function createFromModel(Model $model)
    {
        $transition = new static(['attributes' => static::getMemorizableAttributes($model)]);
        $transition->setSubject($model);

        return $transition;
    }

    /**
     * @param Model $current
     * @return Model
     */
    public function getModelCreatedFromState(Model $current)
    {
        $model = app($this->getSubjectType())->forceFill($this->getProperties()['attributes'])->syncOriginal();
        $model->exists = true;

        return $model;
    }
}
