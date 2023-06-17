<?php


namespace Debuqer\EloquentMemory\Transitions;


use Debuqer\EloquentMemory\StorageModels\TransitionStorageModelContract;
use Debuqer\EloquentMemory\Transitions\Concerns\HasAttributes;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelClass;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelKey;
use Debuqer\EloquentMemory\Transitions\Concerns\HasOldAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ModelDeleted extends BaseTransition implements TransitionInterface
{
    const TypeName = "model-deleted";

    use HasAttributes;

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
     * @return null
     */
    public function getModelCreatedFromState()
    {
        return null;
    }
}
