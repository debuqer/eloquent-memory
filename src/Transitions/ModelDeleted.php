<?php


namespace Debuqer\EloquentMemory\Transitions;


use Debuqer\EloquentMemory\StorageModels\TransitionStorageModelContract;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelClass;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelKey;
use Debuqer\EloquentMemory\Transitions\Concerns\HasOldAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ModelDeleted extends BaseTransition implements TransitionInterface
{
    use HasModelClass;
    use HasOldAttributes;

    public static function createFromModel(Model $model)
    {
        /** @var BaseTransition $transition */
        $transition = new static([
            'old' => static::getMemorizableAttributes($model)
        ]);
        $transition->setSubject($model);

        return $transition;
    }
}
