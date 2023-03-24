<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Illuminate\Database\Eloquent\Model;

class ModelCreated extends BaseChangeType implements ChangeTypeInterface
{
    const TYPE = 'create';

    /** @var array */
    protected $attributes;

    /** @var string */
    protected $modelClass;

    /**
     * ModelCreated constructor.
     * @param string $modelClass
     * @param array $attributes
     */
    public function __construct(string $modelClass, array $attributes)
    {
        $this->modelClass = $modelClass;
        $this->attributes = $attributes;
    }

    public static function create($old, $new): ChangeTypeInterface
    {
        return new self(get_class($new), $new->getAttributes());
    }

    public static function satisfyConditions($old, $new): bool
    {
        return ($new and ! $old);
    }

    public function apply()
    {
        /** @var Model $model */
        $model = app($this->modelClass);

        $model->getConnection()->table($model->getTable())->insert($this->attributes);
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelDeleted($this->modelClass, $this->attributes);
    }
}