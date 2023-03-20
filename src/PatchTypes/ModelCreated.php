<?php


namespace Debuqer\EloquentMemory\PatchTypes;


use Illuminate\Database\Eloquent\Model;

class ModelCreated implements ChangeTypeInterface
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

    public function getType(): string
    {
        return self::TYPE;
    }
}
