<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


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

    public static function satisfyConditions($old, $new): bool
    {
        if ( $new and method_exists($new, 'getDeletedAtColumn') ) {
            /** @var Model $new */
            return (ModelUpdated::satisfyConditions($old, $new) and ! $old->getAttribute($old->getDeletedAtColumn()) and $new->getAttribute($new->getDeletedAtColumn()));
        }

        return false;
    }

    public function apply()
    {
        // since restore is a simple update the model may be back to its last state
        $this->update($this->after, $this->before);
    }


    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelSoftDeleted($this->before, $this->after);
    }
}
