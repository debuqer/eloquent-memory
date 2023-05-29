<?php


namespace Debuqer\EloquentMemory\Transitions;

use Debuqer\EloquentMemory\Facades\EloquentMemory;
use Debuqer\EloquentMemory\StorageModels\ModelTransition;
use Debuqer\EloquentMemory\StorageModels\TransitionStorageModelContract;
use Debuqer\EloquentMemory\StorageModels\TransitionRepository;
use Debuqer\EloquentMemory\Transitions\Concerns\HasProperties;
use Illuminate\Support\Str;

abstract class BaseTransition implements TransitionInterface
{
    use HasProperties;

    protected $model;



    public static function createFromPersistedRecord(TransitionStorageModelContract $change)
    {
        return new static($change->properties);
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

    /**
     * @codeCoverageIgnore
     * @return mixed
     */
    public function down()
    {
        return $this->getRollbackChange()->up();
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
}
