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
    use HasModelKey;
    use HasOldAttributes;

    public static function createFromModel(Model $model)
    {
        return new static([
            'model_class' => get_class($model),
            'key' => $model->getKey(),
            'old' => static::getMemorizableAttributes($model)
        ]);
    }

    public function up()
    {
        $this->getModelObject()->findOrFail($this->getKeyForDeleting())->forceDelete();
    }

    protected function getKeyForDeleting()
    {
        return $this->getOldAttributes()[$this->getModelObject()->getKeyName()];
    }

    public function getRollbackChange(): TransitionInterface
    {
        return new ModelCreated(['model_class' => $this->getModelClass(), 'attributes' => $this->getOldAttributes()]);
    }
}
