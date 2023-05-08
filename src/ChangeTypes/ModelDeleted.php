<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Debuqer\EloquentMemory\Change;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasModelClass;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasModelKey;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasOldAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ModelDeleted extends BaseChangeType implements ChangeTypeInterface
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

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelCreated($this->getModelClass(), $this->getOldAttributes());
    }
}
