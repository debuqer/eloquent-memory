<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Illuminate\Database\Eloquent\Model;

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
        $this->model->save();
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelDeleted($this->model);
    }
}
