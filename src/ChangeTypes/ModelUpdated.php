<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Debuqer\EloquentMemory\Change;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasAttributes;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasModelKey;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasOldAttributes;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasModelClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ModelUpdated extends BaseChangeType implements ChangeTypeInterface
{
    use HasModelClass;
    use HasModelKey;
    use HasOldAttributes;
    use HasAttributes;


    public static function createFromPersistedRecord(Change $change)
    {
        $modelClass = Arr::get($change->parameters, 'model_class');
        $modelKey   = Arr::get($change->parameters, 'model_key');
        $old         = Arr::get($change->parameters, 'old');
        $attributes = Arr::get($change->parameters, 'attributes');

        return new self($modelClass, $modelKey, $old, $attributes);
    }

    public static function createFromModel(Model $model)
    {
        return new self(get_class($model), $model->getKey(), $model->getRawOriginal(), $model->getAttributes());
    }
    /**
     * ModelUpdated constructor.
     * @param string $modelClass
     * @param array $before
     * @param array $after
     */
    public function __construct(string $modelClass, $modelKey, array $old, array $attributes)
    {
        $this->setModelClass($modelClass);
        $this->setModelKey($modelKey);
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
        $this->getModelInstance()->withTrashed()->findOrFail($this->getModelKey())->update($update);
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

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelUpdated($this->getModelClass(), $this->getModelKey(), $this->getAttributes(), $this->getOldAttributes());
    }
}
