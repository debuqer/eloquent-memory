<?php

namespace Debuqer\EloquentMemory\Transitions;

use Debuqer\EloquentMemory\Repositories\TransitionPersistDriverInterface;
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
     *  The unique name of transition
     *  Will play the role of key in config.changes
     */
    public const TypeName = '';

    /**
     * @param PersistedTransitionRecordInterface $persistedTransitionRecord
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
        return $model->getMemorizableAttributes();
    }

    /**
     * unique identifier for pair of (subject_type, subject_id)
     * as a database index facilitates searching
     *
     * @return string
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
        return $this->getProperties()['attributes'][app($this->getSubjectType())->getKeyName()] ?? "";
    }
}
