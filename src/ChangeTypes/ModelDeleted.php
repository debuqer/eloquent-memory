<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemExists;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemNotExists;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasAttributes;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasModelClass;
use Illuminate\Database\Eloquent\Model;

class ModelDeleted extends BaseChangeType implements ChangeTypeInterface
{
    use HasModelClass;
    use HasAttributes;

    const TYPE = 'delete';

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
        return (
            ItemExists::setItem($old)->evaluate() and
            ItemNotExists::setItem($new)->evaluate()
        );
    }

    public function up()
    {
        app($this->modelClass)->findOrFail($this->getKeyForDeleting())->forceDelete();
    }

    protected function getKeyForDeleting()
    {
        /** @var Model $model */
        $model = app($this->modelClass);

        return $this->attributes[$model->getKeyName()] ?? $model->getKey();
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelCreated($this->modelClass, $this->attributes);
    }
}
