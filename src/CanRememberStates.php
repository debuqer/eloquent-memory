<?php

namespace Debuqer\EloquentMemory;

use Carbon\Carbon;
use Debuqer\EloquentMemory\Repositories\PersistedTransitionRecordInterface;
use Debuqer\EloquentMemory\Repositories\TransitionQuery;
use Debuqer\EloquentMemory\Repositories\TransitionPersistDriver;

trait CanRememberStates
{
    public static function booted(): void
    {
        static::observe(StateRememberObserver::class);
    }

    /**
     * Will override in case user wish to exclude or include some attributes
     *
     * @return array|mixed
     */
    public function getMemorizableAttributes()
    {
        return $this->getRawOriginal();
    }

    /**
     * Will generate a unique identifier of model which can be used for addressing a model
     * Can be overridden
     *
     *
     * @return string
     */
    public function getModelIdentifier()
    {
        return md5(get_class($this).serialize($this->getKey()));
    }

    /**
     * Will query for one ModelState at a specific given time
     *
     * @param  Carbon  $givenTime
     * @return null
     */
    public function getStateOf(Carbon $givenTime)
    {
        $query = TransitionQuery::create()
            ->setConditions([
                ['subject_type', '=', get_class($this)],
            ])->setUntil($givenTime)
            ->setTake(1);

        $transitionRepository = app(TransitionPersistDriver::class)->find($query);

        /** @var PersistedTransitionRecordInterface $state */
        $state = $transitionRepository->current();

        if (! $state or $state->getTransition()->getType() == 'model-deleted') {
            return null;  // model not exists
        } else {
            return $state->getTransition()->getModelCreatedFromState($this);
        }
    }

    /**
     * Can change property of exists from outside
     *
     * @param  bool  $exists
     * @return $this
     */
    public function setExists($exists = true)
    {
        $this->exists = $exists;

        return $this;
    }
}
