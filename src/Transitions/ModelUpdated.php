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
    const TypeName = "model-updated";

    use HasAttributes;

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
     * @return mixed
     */
    public function getModelCreatedFromState()
    {
        $model = app($this->getSubjectType())->forceFill($this->getProperties()['attributes'])->syncOriginal();
        $model->exists = true;

        return $model;
    }
}
