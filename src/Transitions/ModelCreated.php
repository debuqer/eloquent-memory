<?php


namespace Debuqer\EloquentMemory\Transitions;

use Debuqer\EloquentMemory\Models\ModelTransitionInterface;
use Debuqer\EloquentMemory\Transitions\Concerns\HasAttributes;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ModelCreated extends BaseTransition implements TransitionInterface
{
    use HasModelClass;
    use HasAttributes;

    public static function createFromModel(Model $model)
    {
        return new static(['model_class' => get_class($model), 'attributes' => $model->getRawOriginal()]);
    }

    public function up()
    {
        $this->getModelInstance()->setRawAttributes($this->getAttributes())->save();
    }

    public function getRollbackChange(): TransitionInterface
    {
        return new ModelDeleted(['model_class' => $this->getModelClass(), 'old' => $this->getAttributes()]);
    }
}
