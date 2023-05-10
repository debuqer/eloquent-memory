<?php


namespace Debuqer\EloquentMemory\Transitions;


use Debuqer\EloquentMemory\Models\ModelTransitionInterface;
use Debuqer\EloquentMemory\Transitions\Concerns\HasAttributes;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelKey;
use Debuqer\EloquentMemory\Transitions\Concerns\HasOldAttributes;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ModelUpdated extends BaseTransition implements TransitionInterface
{
    use HasModelClass;
    use HasModelKey;
    use HasOldAttributes;
    use HasAttributes;


    public static function createFromPersistedRecord(ModelTransitionInterface $change)
    {
        $modelClass = Arr::get($change->parameters, 'model_class');
        $modelKey   = Arr::get($change->parameters, 'key');
        $old         = Arr::get($change->parameters, 'old');
        $attributes = Arr::get($change->parameters, 'attributes');

        return new static($modelClass, $modelKey, $old, $attributes);
    }

    public static function createFromModel(Model $before, Model $after)
    {
        return new static(get_class($after), $after->getKey(), $before->getRawOriginal(), $after->getRawOriginal());
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
        $this->getModelInstance()->findOrFail($this->getModelKey())->update($update);
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

    public function getRollbackChange(): TransitionInterface
    {
        return new ModelUpdated($this->getModelClass(), $this->getModelKey(), $this->getAttributes(), $this->getOldAttributes());
    }
}
