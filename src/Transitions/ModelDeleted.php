<?php


namespace Debuqer\EloquentMemory\Transitions;


use Debuqer\EloquentMemory\Change;
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

    public static function createFromPersistedRecord(Change $change)
    {
        $modelClass = Arr::get($change->parameters, 'model_class');
        $old = Arr::get($change->parameters, 'old');

        return new static($modelClass, $old);
    }

    public static function createFromModel(Model $model)
    {
        return new static(get_class($model), $model->getRawOriginal());
    }

    /**
     * ModelCreated constructor.
     * @param string $modelClass
     * @param array $attributes
     */
    public function __construct(string $modelClass, array $attributes)
    {
        $this->setModelClass($modelClass);
        $this->setOldAttributes($attributes);
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
        return new ModelCreated($this->getModelClass(), $this->getOldAttributes());
    }
}
