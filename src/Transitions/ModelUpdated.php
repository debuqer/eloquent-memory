<?php


namespace Debuqer\EloquentMemory\Transitions;


use Debuqer\EloquentMemory\StorageModels\TransitionStorageModelContract;
use Debuqer\EloquentMemory\Transitions\Concerns\HasAttributes;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelKey;
use Debuqer\EloquentMemory\Transitions\Concerns\HasOldAttributes;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use PhpParser\Node\Expr\AssignOp\Mod;

class ModelUpdated extends BaseTransition implements TransitionInterface
{
    use HasModelClass;
    use HasModelKey;
    use HasOldAttributes;
    use HasAttributes;

    public static function createFromModel(Model $before, Model $after)
    {
        return new static([
            'model_class' => get_class($after),
            'key' => $after->getKey(),
            'old' => static::getMemorizableAttributes($before),
            'attributes' => static::getMemorizableAttributes($after)
        ]);
    }
}
