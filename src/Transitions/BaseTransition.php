<?php


namespace Debuqer\EloquentMemory\Transitions;

use Debuqer\EloquentMemory\Facades\EloquentMemory;
use Debuqer\EloquentMemory\StorageModels\ModelTransition;
use Debuqer\EloquentMemory\StorageModels\TransitionStorageModelContract;
use Debuqer\EloquentMemory\StorageModels\TransitionRepository;
use Debuqer\EloquentMemory\Transitions\Concerns\HasProperties;
use Debuqer\EloquentMemory\Transitions\Concerns\HasSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class BaseTransition implements TransitionInterface
{
    use HasProperties;
    use HasSubject;

    protected $model;



    public static function createFromPersistedRecord(TransitionStorageModelContract $change)
    {
        $transition = new static($change->properties);

        $attributes = $transition->getProperties()['attributes'] ?? [];
        $subjectClass = $change->model_class;
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

    public function getType(): string
    {
        return Str::kebab($this->getClassName());
    }

    protected function getClassName()
    {
        return explode('\\', get_class($this))[count(explode('\\', get_class($this)))-1];
    }

    public function persist()
    {
        $this->model = app(TransitionRepository::class)->persist($this);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public static function getMemorizableAttributes(Model $model)
    {
        if ( method_exists($model, 'getMemorizableAttributes') ) {
            return $model->getMemorizableAttributes();
        }

        return $model->getRawOriginal();
    }

    public function getTransitionStorageAddress(): string
    {
        return md5($this->getSubjectClass());
    }
}
