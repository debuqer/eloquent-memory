<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Illuminate\Database\Eloquent\Model;

class ModelDeleted implements ChangeTypeInterface
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

    public function getType(): string
    {
        return self::TYPE;
    }

    public function apply()
    {
        $this->model->delete();
    }


    public function rollback()
    {
        return $this->getRollbackChange()->apply();
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelCreated($this->model);
    }
}
