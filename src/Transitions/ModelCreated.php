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


    public static function createFromPersistedRecord(ModelTransitionInterface $change)
    {
        return app(static::class, ['parameters' => (array) $change->parameters]);
    }

    public static function createFromModel(Model $model)
    {
        return new static(['model_class' => get_class($model), 'attributes' => $model->getRawOriginal()]);
    }

    /**
     * ModelCreated constructor.
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->setParameters($parameters);
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
