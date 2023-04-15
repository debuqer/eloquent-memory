<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemsAreTheSame;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemExists;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsModel;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsNotNull;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsNotTrash;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemNotExists;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemsAreTheSameType;
use Illuminate\Database\Eloquent\Model;

class ModelUpdated extends BaseChangeType implements ChangeTypeInterface
{
    const TYPE = 'update';

    /** @var string */
    protected $modelClass;
    /** @var array  */
    protected $before;
    /** @var array  */
    protected $after;

    /**
     * ModelUpdated constructor.
     * @param string $modelClass
     * @param array $before
     * @param array $after
     */
    public function __construct(string $modelClass, array $before, array $after)
    {
        $this->modelClass = $modelClass;
        $this->before = $before;
        $this->after = $after;
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
        $this->getModelInstance()->findOrFail($this->getModelKey($this->after))->update($update);
    }

    protected function getAllAttributes()
    {
        return array_keys(array_merge($this->before, $this->after));
    }

    protected function getChangedValues()
    {
        $allAttributes = $this->getAllAttributes();

        $update = [];
        array_map(function ($attribute) use(&$update) {
            $valueBeforeChange = isset($this->before[$attribute]) ? $this->before[$attribute] : null;
            $valueAfterChange = isset($this->after[$attribute]) ? $this->after[$attribute] : null;

            if ( $valueAfterChange !== $valueBeforeChange ) {
                $update[$attribute] = $valueAfterChange;
            }
        }, $allAttributes);

        return $update;
    }

    protected function getModelInstance()
    {
        return app($this->modelClass);
    }

    protected function getModelKey($attributes)
    {
        return isset($attributes[$this->getModelInstance()->getKeyName()]) ? $attributes[$this->getModelInstance()->getKeyName()] : null;
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelUpdated($this->modelClass, $this->before, $this->after);
    }
}
