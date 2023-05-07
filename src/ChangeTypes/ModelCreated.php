<?php


namespace Debuqer\EloquentMemory\ChangeTypes;

use Debuqer\EloquentMemory\Change;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasAttributes;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasModelClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ModelCreated extends BaseChangeType implements ChangeTypeInterface
{
    use HasModelClass;
    use HasAttributes;


    public static function createFromPersistedRecord(Change $change)
    {
        $modelClass = Arr::get($change->parameters, 'model_class');
        $attributes = Arr::get($change->parameters, 'attributes');

        return new self($modelClass, $attributes);
    }

    public static function createFromModel(Model $model)
    {
        return new self(get_class($model), $model->getRawOriginal());
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

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelDeleted($this->getModelClass(), $this->getAttributes());
    }
}
