<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public static function create($old, $new): ChangeTypeInterface
    {
        return new self($old, $new);
    }

    public static function satisfyConditions($old, $new): bool
    {
        if ( ! $old ) {
            return false;
        }
        if ( ! $new ) {
            return false;
        }
        if ( ! $old instanceof Model ) {
            return false;
        }
        if ( get_class($old) !== get_class($new) ) {
            return false;
        }

        return count(array_merge(array_diff($old->getAttributes(), $new->getAttributes()), array_diff($new->getAttributes(), $old->getAttributes()))) > 0;
    }

    public function apply()
    {
        /** @var Model $model */
        $model = app(get_class($this->before));

        DB::table($model->getTable())->where($model->getKeyName(), $this->before->getKey())->update($this->after->getAttributes());
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelUpdated($this->before, $this->after);
    }
}
