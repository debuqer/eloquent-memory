<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemExists;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsNotTrash;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsTrash;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemsAreTheSame;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ModelRestored extends BaseChangeType implements ChangeTypeInterface
{
    use UpdatesModelTrait;

    const TYPE = 'restore';

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
            ItemIsTrash::setItem($old)->evaluate() and
            ItemIsNotTrash::setItem($new)->evaluate() and
            ItemsAreTheSame::setItem($old)->setExpect($new)->evaluate()
        );
    }

    public function up()
    {
        // since restore is a simple update the model may be back to its last state
        $this->update($this->before, $this->after);
    }


    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelSoftDeleted($this->before, $this->after);
    }
}
