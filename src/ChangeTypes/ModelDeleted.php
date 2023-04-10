<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Illuminate\Database\Eloquent\Model;

class ModelDeleted extends BaseChangeType implements ChangeTypeInterface
{
    const TYPE = 'delete';

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
        return new self(get_class($old), $old->getAttributes());
    }

    public static function isApplicable($old, $new): bool
    {
        return (! $new and $old);
    }

    public function apply()
    {
        /** @var Model $model */
        $model = app($this->modelClass);

        $model->getConnection()->table($model->getTable())->delete($this->attributes[$model->getKeyName()]);
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelCreated($this->modelClass, $this->attributes);
    }
}
