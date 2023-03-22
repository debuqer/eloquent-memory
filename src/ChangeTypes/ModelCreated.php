<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModelCreated extends BaseChangeType implements ChangeTypeInterface
{
    const TYPE = 'create';

    /** @var Model */
    protected $model;
    /**
     * ModelCreated constructor.
     * @param Model $createdModel
     */
    public function __construct(Model $createdModel)
    {
        $this->model = $createdModel;
    }

    public static function create($old, $new): ChangeTypeInterface
    {
        return new self($new);
    }

    public static function satisfyConditions($old, $new): bool
    {
        return ($new and ! $old);
    }

    public function apply()
    {
        /** @var Model $model */
        $model = app(get_class($this->model));

        $model->getConnection()->table($model->getTable())->insert($this->model->getAttributes());
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelDeleted($this->model);
    }
}
