<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


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
        if ( $new and method_exists($new, 'getDeletedAtColumn') ) {
            /** @var Model $new */
            return (ModelUpdated::isApplicable($old, $new) and ! $old->getAttribute($old->getDeletedAtColumn()) and $new->getAttribute($new->getDeletedAtColumn()));
        }

        return false;
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
