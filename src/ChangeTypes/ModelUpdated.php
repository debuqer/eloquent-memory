<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasAttributes;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasOldAttributes;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasModelClass;

class ModelUpdated extends BaseChangeType implements ChangeTypeInterface
{
    use HasModelClass;
    use HasOldAttributes;
    use HasAttributes;

    /**
     * ModelUpdated constructor.
     * @param string $modelClass
     * @param array $before
     * @param array $after
     */
    public function __construct(string $modelClass, array $old, array $attributes)
    {
        $this->setModelClass($modelClass);
        $this->setOldAttributes($old);
        $this->setAttributes($attributes);
    }

    public function up()
    {
        $update = $this->getChangedValues();

        $this->update($update);
    }

    protected function update(array $update)
    {
        $this->getModelInstance()->withTrashed()->findOrFail($this->getModelKey($this->getOldAttributes()))->update($update);
    }

    protected function getAllAttributes()
    {
        return array_keys(array_merge($this->getOldAttributes(), $this->getAttributes()));
    }

    protected function getChangedValues()
    {
        $allAttributes = $this->getAllAttributes();

        $update = [];
        array_map(function ($attribute) use(&$update) {
            $valueBeforeChange = isset($this->getOldAttributes()[$attribute]) ? $this->getOldAttributes()[$attribute] : null;
            $valueAfterChange = isset($this->getAttributes()[$attribute]) ? $this->getAttributes()[$attribute] : null;

            if ( $valueAfterChange !== $valueBeforeChange ) {
                $update[$attribute] = $valueAfterChange;
            }
        }, $allAttributes);

        return $update;
    }

    protected function getModelKey($attributes)
    {
        return isset($attributes[$this->getModelInstance()->getKeyName()]) ? $attributes[$this->getModelInstance()->getKeyName()] : null;
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelUpdated($this->getModelClass(), $this->getAttributes(), $this->getOldAttributes());
    }
}
