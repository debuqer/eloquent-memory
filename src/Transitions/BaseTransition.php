<?php


namespace Debuqer\EloquentMemory\Transitions;

use Debuqer\EloquentMemory\StorageModels\TransitionStorageModelContract;
use Debuqer\EloquentMemory\StorageModels\TransitionRepository;
use Debuqer\EloquentMemory\Transitions\Concerns\HasProperties;
use Debuqer\EloquentMemory\Transitions\Concerns\HasSubject;
use Illuminate\Database\Eloquent\Model;

abstract class BaseTransition implements TransitionInterface
{
    use HasProperties;
    use HasSubject;

    const TypeName = "";

    protected $model;


    /**
     * @param TransitionStorageModelContract $change
     * @return static
     */
    public static function createFromPersistedRecord(TransitionStorageModelContract $change)
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

    public function persist(): void
    {
        $this->model = app(TransitionRepository::class)->persist($this);
    }

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
