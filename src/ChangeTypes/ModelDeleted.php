<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Illuminate\Database\Eloquent\Model;

class ModelDeleted extends BaseChangeType implements ChangeTypeInterface
{
    const TYPE = 'delete';

    /** @var Model */
    protected $model;
    /**
     * ModelCreated constructor.
     * @param Model $deletedModel
     */
    public function __construct(Model $deletedModel)
    {
        $this->model = $deletedModel;
    }

    public function apply()
    {
        $this->model->forceDelete();
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelCreated($this->model);
    }
}
