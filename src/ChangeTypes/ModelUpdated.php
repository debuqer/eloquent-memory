<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Illuminate\Database\Eloquent\Model;

class ModelUpdated extends BaseChangeType implements ChangeTypeInterface
{
    use UpdatesModelTrait;

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

    public static function isApplicable($old, $new): bool
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

        $allAttributes = array_merge(array_keys($old->getAttributes()), array_keys($new->getAttributes()));
        $diff = [];
        foreach ($allAttributes as $attribute ) {
            if ( $old->getAttribute($attribute) !== $new->getAttribute($attribute) ) {
                $diff[] = $attribute;
            }
        }

        return count($diff) > 0;
    }

    public function apply()
    {
        $this->update($this->before, $this->after);
    }

    public function getRollbackChange(): ChangeTypeInterface
    {
        return new ModelUpdated($this->before, $this->after);
    }
}
