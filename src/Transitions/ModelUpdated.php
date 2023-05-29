<?php


namespace Debuqer\EloquentMemory\Transitions;


use Debuqer\EloquentMemory\StorageModels\TransitionStorageModelContract;
use Debuqer\EloquentMemory\Transitions\Concerns\HasAttributes;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelKey;
use Debuqer\EloquentMemory\Transitions\Concerns\HasOldAttributes;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use PhpParser\Node\Expr\AssignOp\Mod;

class ModelUpdated extends BaseTransition implements TransitionInterface
{
    use HasModelClass;
    use HasModelKey;
    use HasOldAttributes;
    use HasAttributes;

    public static function createFromChanges(Model $origin, array $changedAttributes)
    {
        return new static([
            'model_class' => get_class($origin),
            'key' => $origin->getKey(),
            'old' => $origin->getRawOriginal(),
            'attributes' => array_merge($origin->getRawOriginal(), $changedAttributes)
        ]);
    }

    public static function createFromModel(Model $before, Model $after)
    {
        return new static([
            'model_class' => get_class($after),
            'key' => $after->getKey(),
            'old' => $before->getRawOriginal(),
            'attributes' => $after->getRawOriginal()
        ]);
    }

    public function up()
    {
        $update = $this->getChangedValues();

        $this->update($update);
    }

    protected function update(array $update)
    {
        /** @var Model $model */
        $model = $this->getModelInstance()->findOrFail($this->getModelKey());
        $model->unguard();
        $model->forceFill($update)->save();
    }

    protected function getModelInstance()
    {
        return $this->getModelObject();
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
        return new ModelUpdated([
            'model_class' => $this->getModelClass(),
            'key' => $this->getModelKey(),
            'old' => $this->getAttributes(),
            'attributes' => $this->getOldAttributes()
        ]);
    }
}
