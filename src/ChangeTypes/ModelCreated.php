<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsModel;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemExists;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsNotModel;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsNotNull;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsNull;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemNotExists;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemsAreNotTheSame;
use Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemsAreTheSame;
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
        if ( ItemIsNotModel::define($new)->evaluate() or ItemNotExists::define($new)->evaluate() ) {
            return false;
        }
        if ( ItemIsNotNull::define($old)->evaluate() and ItemIsNotModel::define($old)->evaluate() ) {
            return false;
        }
        if ( ItemIsModel::define($old)->evaluate() and ItemExists::define($old)->evaluate() ) {
            return false;
        }
        if ( ItemIsModel::define($old)->evaluate() and ItemsAreNotTheSame::define($old)->setExpect($new)->evaluate() ) {
            return false;
        }

        return true;
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
