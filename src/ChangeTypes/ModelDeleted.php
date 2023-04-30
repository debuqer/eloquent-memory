<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasModelClass;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasModelKey;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasOldAttributes;

class ModelDeleted extends BaseChangeType implements ChangeTypeInterface
{
    use HasModelClass;
    use HasModelKey;
    use HasOldAttributes;

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
