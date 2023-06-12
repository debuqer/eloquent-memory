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
    use HasOldAttributes;
    use HasAttributes;

    public static function createFromModel(Model $before, Model $after)
    {
        $transition = new static(['attributes' => static::getMemorizableAttributes($before)]);
        $transition->setSubject($before);

        return $transition;
    }
}
