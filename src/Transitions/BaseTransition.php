<?php

namespace Debuqer\EloquentMemory\Transitions;

use Debuqer\EloquentMemory\Repositories\PersistedTransitionRecordInterface;
use Debuqer\EloquentMemory\Repositories\TransitionRepository;
use Debuqer\EloquentMemory\Transitions\Concerns\HasAttributes;
use Debuqer\EloquentMemory\Transitions\Concerns\HasProperties;
use Debuqer\EloquentMemory\Transitions\Concerns\HasSubject;
use Illuminate\Database\Eloquent\Model;

abstract class BaseTransition implements TransitionInterface
{
    use HasProperties;
    use HasAttributes;
    use HasSubject;

    /**
     * @return static
     */
    public static function createFromPersistedRecord(PersistedTransitionRecordInterface $persistedTransitionRecord)
    {
        $transition = new static($persistedTransitionRecord->getProperties());

        $attributes = $transition->getProperties()['attributes'] ?? [];
        $subjectClass = $persistedTransitionRecord->getSubjectType();
        $subject = app($subjectClass)->forceFill($attributes);

        $transition->setSubject($subject);

        return $transition;
    }

    /**
     * @return static
     */
    public static function createFromModel(Model $model)
    {
        $transition = new static(['attributes' => static::getMemorizableAttributes($model)]);
        $transition->setSubject($model);

        return $transition;
    }

    public function __construct(array $properties)
    {
        $this->setProperties($properties);
    }

    public function persist(): void
    {
        app(TransitionRepository::class)->persist($this);
    }

    public function getType(): string
    {
        return static::TypeName;
    }

    /**
     * @return array|mixed
     */
    public static function getMemorizableAttributes(Model $model)
    {
        return $model->getMemorizableAttributes();
    }

    /**
     * unique identifier for pair of (subject_type, subject_id)
     * as a database index facilitates searching
     */
    public function getTransitionStorageAddress(): string
    {
        return $this->getSubject()->getModelAddress();
    }

    /**
     * @return string
     */
    public function getSubjectKey()
    {
        return $this->getProperties()['attributes'][app($this->getSubjectType())->getKeyName()] ?? '';
    }
}
