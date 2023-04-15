<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemsAreTheSame;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemExists;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsModel;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsNotNull;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsNotTrash;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemNotExists;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemsAreTheSameType;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasAfterAttributes;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasBeforeAttributes;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasModelClass;
use Illuminate\Database\Eloquent\Model;

class ModelUpdated extends BaseChangeType implements ChangeTypeInterface
{
    use HasModelClass;
    use HasBeforeAttributes;
    use HasAfterAttributes;

    const TYPE = 'update';

    /**
     * ModelUpdated constructor.
     * @param string $modelClass
     * @param array $before
     * @param array $after
     */
    public function __construct(string $modelClass, array $before, array $after)
    {
        $this->setModelClass($modelClass);
        $this->setBeforeAttributes($before);
        $this->setAfterAttributes($after);
    }

    public static function create($old, $new): ChangeTypeInterface
    {
        return new self(get_class($new), $old->getRawOriginal(), $new->getRawOriginal());
    }

    public static function isApplicable($old, $new): bool
    {
        return (
            ItemExists::setItem($old)->evaluate() and
            ItemExists::setItem($new)->evaluate() and
            ItemIsNotTrash::setItem($old)->evaluate() and
            ItemIsNotTrash::setItem($new)->evaluate() and
            ItemsAreTheSame::setItem($old)->setExpect($new)->evaluate() and
            static::attributeChanged($old, $new)
        );
    }

    public static function attributeChanged($old, $new)
    {
        $allAttributes = array_merge(array_keys($old->getRawOriginal()), array_keys($new->getRawOriginal()));
        foreach ($allAttributes as $attribute ) {
            if ( $old->getRawOriginal($attribute) !== $new->getRawOriginal($attribute) ) {
                return true;
            }
        }

        return false;
    }

    public function up()
    {
        $update = $this->getChangedValues();

        $this->update($update);
    }

    protected function update(array $update)
    {
        $this->getModelInstance()->withTrashed()->findOrFail($this->getModelKey($this->getBeforeAttributes()))->update($update);
    }

    protected function getAllAttributes()
    {
        return array_keys(array_merge($this->getBeforeAttributes(), $this->getAfterAttributes()));
    }

    protected function getChangedValues()
    {
        $allAttributes = $this->getAllAttributes();

        $update = [];
        array_map(function ($attribute) use(&$update) {
            $valueBeforeChange = isset($this->getBeforeAttributes()[$attribute]) ? $this->getBeforeAttributes()[$attribute] : null;
            $valueAfterChange = isset($this->getAfterAttributes()[$attribute]) ? $this->getAfterAttributes()[$attribute] : null;

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
        return new ModelUpdated($this->getModelClass(), $this->getAfterAttributes(), $this->getBeforeAttributes());
    }
}
