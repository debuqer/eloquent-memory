<?php


namespace Debuqer\EloquentMemory\Transitions;


use Debuqer\EloquentMemory\Models\ModelTransitionInterface;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelClass;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelKey;
use Debuqer\EloquentMemory\Transitions\Concerns\HasOldAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ModelDeleted extends BaseTransition implements TransitionInterface
{
    use HasModelClass;
    use HasModelKey;
    use HasOldAttributes;

    public static function createFromModel(Model $model)
    {
        return new static([
            'model_class' => get_class($model),
            'key' => $model->getKey(),
            'old' => $model->getRawOriginal()
        ]);
    }

    public function up()
    {
        $this->getModelInstance()->findOrFail($this->getKeyForDeleting())->forceDelete();
    }

    protected function getKeyForDeleting()
    {
        return $this->getOldAttributes()[$this->getModelInstance()->getKeyName()];
    }

    public function getRollbackChange(): TransitionInterface
    {
        return new ModelCreated(['model_class' => $this->getModelClass(), 'attributes' => $this->getOldAttributes()]);
    }
}
