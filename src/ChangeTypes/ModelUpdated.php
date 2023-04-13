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
    use UpdatesModelTrait;

    const TYPE = 'update';

    /** @var Model */
    protected $before;
    /** @var Model */
    protected $after;
    /**
     * ModelCreated constructor.
     * @param Model $updatedModel
     */
    public function __construct(Model $before, Model $after)
    {
        $this->before = $before;
        $this->after = $after;
    }

    public static function create($old, $new): ChangeTypeInterface
    {
        return new self($old, $new);
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

    public function apply()
    {
        $this->update($this->before, $this->after);
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelUpdated($this->before, $this->after);
    }
}
