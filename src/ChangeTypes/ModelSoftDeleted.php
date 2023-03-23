<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ModelSoftDeleted extends BaseChangeType implements ChangeTypeInterface
{
    const TYPE = 'softDelete';

    /** @var Model */
    protected $model;

    /**
     * ModelCreated constructor.
     * @param Model $updatedModel
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public static function create($old, $new): ChangeTypeInterface
    {
        return new self($new);
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
        $this->model->getConnection()
            ->table($this->model->getTable())
            ->where($this->model->getKeyName(), $this->model->getKey())
            ->update($this->model->getAttributes());
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
       // return new ModelUpdated($this->before, $this->after);
    }
}
