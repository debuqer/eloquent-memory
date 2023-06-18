<?php


namespace Debuqer\EloquentMemory\Transitions;

use Debuqer\EloquentMemory\StorageModels\TransitionRepositoryInterface;
use Debuqer\EloquentMemory\StorageModels\TransitionRepository;
use Debuqer\EloquentMemory\Transitions\Concerns\HasProperties;
use Debuqer\EloquentMemory\Transitions\Concerns\HasSubject;
use Illuminate\Database\Eloquent\Model;

abstract class BaseTransition implements TransitionInterface
{
    use HasProperties;
    use HasSubject;

    /**
     *  The unique name of transition
     *  Will play the role of key in config.changes
     */
    const TypeName = "";

    /**
     * @param TransitionRepositoryInterface $change
     * @return static
     */
    public static function createFromPersistedRecord(TransitionRepositoryInterface $change)
    {
        $transition = new static($change->properties);

        $attributes = $transition->getProperties()['attributes'] ?? [];
        $subjectClass = $change->subject_type;
        $subject = new $subjectClass($attributes);

        $transition->setSubject($subject);

        return $transition;
    }

    /**
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->setProperties($properties);
    }

    /**
     *
     */
    public function persist(): void
    {
        app(TransitionRepository::class)->persist($this);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return static::TypeName;
    }

    /**
     * @param Model $model
     * @return array|mixed
     */
    public static function getMemorizableAttributes(Model $model)
    {
        if ( method_exists($model, 'getMemorizableAttributes') ) {
            return $model->getMemorizableAttributes();
        }

        return $model->getRawOriginal();
    }

    /**
     * unique identifier for pair of (subject_type, subject_id)
     * as a database index facilitates searching
     *
     * @return string
     */
    public function getTransitionStorageAddress(): string
    {
        return md5($this->getSubject()->getModelAddress());
    }

    /**
     * @return string
     */
    public function getSubjectKey()
    {
        return $this->getProperties()['attributes'][app($this->getSubjectType())->getKeyName()] ?? "";
    }

    /**
     * @return mixed|string
     */
    protected function getClassName()
    {
        return explode('\\', get_class($this))[count(explode('\\', get_class($this)))-1];
    }
}
