<?php


namespace Debuqer\EloquentMemory\Transitions;

use Debuqer\EloquentMemory\Change;
use Debuqer\EloquentMemory\Models\ModelTransitionInterface;
use Debuqer\EloquentMemory\Transitions\Concerns\HasAttributes;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ModelCreated extends BaseTransition implements TransitionInterface
{
    use HasModelClass;
    use HasAttributes;


    public static function createFromPersistedRecord(ModelTransitionInterface $change)
    {
        $modelClass = Arr::get($change->parameters, 'model_class');
        $attributes = Arr::get($change->parameters, 'attributes');

        return new static($modelClass, $attributes);
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
        $this->setAttributes($attributes);
    }

    public function up()
    {
        $this->getModelInstance()->setRawAttributes($this->getAttributes())->save();
    }

    public function getRollbackChange(): TransitionInterface
    {
        return new ModelDeleted($this->getModelClass(), $this->getAttributes());
    }
}
