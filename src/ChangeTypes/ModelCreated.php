<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemExists;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsNull;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasAttributes;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasModelClass;

class ModelCreated extends BaseChangeType implements ChangeTypeInterface
{
    use HasModelClass;
    use HasAttributes;

    /**
     * ModelCreated constructor.
     * @param string $modelClass
     * @param array $attributes
     */
    public function __construct(string $modelClass, array $attributes)
    {
        $this->setModelClass($modelClass);
        $this->setAttributes($attributes);
    }

    public static function create($old, $new): ChangeTypeInterface
    {
        return new self(get_class($new), $new->getAttributes());
    }

    public static function isApplicable($old, $new): bool
    {
        return (
            ItemIsNull::setItem($old)->evaluate() and
            ItemExists::setItem($new)->evaluate()
        );
    }

    public function up()
    {
        $this->getModelInstance()->setRawAttributes($this->getAttributes())->save();
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelDeleted($this->getModelClass(), $this->getAttributes());
    }
}
