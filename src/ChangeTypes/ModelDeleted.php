<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasAttributes;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasModelClass;

class ModelDeleted extends BaseChangeType implements ChangeTypeInterface
{
    use HasModelClass;
    use HasAttributes;

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

    public static function create($old, $new): ChangeTypeInterface
    {
        return new self(get_class($old), $old->getAttributes());
    }

    public function up()
    {
        $this->getModelInstance()->findOrFail($this->getKeyForDeleting())->forceDelete();
    }

    protected function getKeyForDeleting()
    {
        return $this->getAttributes()[$this->getModelInstance()->getKeyName()];
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelCreated($this->getModelClass(), $this->getAttributes());
    }
}
