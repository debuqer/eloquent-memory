<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemExists;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsNotTrash;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsTrash;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemsAreTheSame;
use Illuminate\Database\Eloquent\Model;

class ModelSoftDeleted extends BaseChangeType implements ChangeTypeInterface
{
    use UpdatesModelTrait;

    const TYPE = 'softDelete';

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
            ItemIsTrash::setItem($new)->evaluate() and
            ItemIsNotTrash::setItem($old)->evaluate() and
            ItemsAreTheSame::setItem($old)->setExpect($new)->evaluate()
        );
    }

    public function apply()
    {
        // since the softDelete is only a simple update the model may change to its final state
        $this->update($this->before, $this->after);
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelRestored($this->after, $this->before);
    }
}
