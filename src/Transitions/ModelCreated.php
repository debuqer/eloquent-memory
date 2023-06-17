<?php


namespace Debuqer\EloquentMemory\Transitions;

use Debuqer\EloquentMemory\Transitions\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;

class ModelCreated extends BaseTransition implements TransitionInterface
{
    const TypeName = "model-created";

    use HasAttributes;

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
     * @return mixed
     */
    public function getModelCreatedFromState()
    {
        $model = app($this->getSubjectType())->forceFill($this->getProperties()['attributes'])->syncOriginal();
        $model->exists = true;

        return $model;
    }
}
