<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Illuminate\Database\Eloquent\Model;

class ModelUpdated extends BaseChangeType implements ChangeTypeInterface
{
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

    public function apply()
    {
        $this->model->update([

        ]);
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelUpdated($this->before, $this->after);
    }
}
