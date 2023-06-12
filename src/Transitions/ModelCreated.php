<?php


namespace Debuqer\EloquentMemory\Transitions;

use Debuqer\EloquentMemory\StorageModels\TransitionStorageModelContract;
use Debuqer\EloquentMemory\Transitions\Concerns\HasAttributes;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelClass;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ModelCreated extends BaseTransition implements TransitionInterface
{
    use HasModelClass;
    use HasAttributes;

    /**
     * @param Model $model
     * @return TransitionInterface
     */
    public static function createFromModel(Model $model): TransitionInterface
    {
        return new static(['model_class' => get_class($model), 'attributes' => static::getMemorizableAttributes($model)]);
    }
}
