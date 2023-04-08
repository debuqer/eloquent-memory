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

    public static function isApplicable($old, $new): bool
    {
        return (
            is_a($new, Model::class)
            and $new->exists
            and $new
            and (!$old or (is_a($old, Model::class) and ! $old->exists and get_class($old) === get_class($new)) )
        );
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
