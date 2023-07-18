<?php

namespace Debuqer\EloquentMemory;

use Carbon\Carbon;
use Debuqer\EloquentMemory\Repositories\PersistedTransitionRecordInterface;
use Debuqer\EloquentMemory\Repositories\TransitionQuery;
use Debuqer\EloquentMemory\Repositories\TransitionRepository;

trait CanRememberStates
{
    /**
     * @return array|mixed
     */
    public function getMemorizableAttributes()
    {
        return $this->getRawOriginal();
    }


    public static function booted(): void
    {
        static::observe(StateRememberObserver::class);
    }

    /**
     * @return string
     */
    public function getModelAddress()
    {
        return md5(get_class($this).serialize($this->getKey()));
    }

    /**
     * @param Carbon $givenTime
     * @return null
     */
    public function getStateOf(Carbon $givenTime)
    {
        $query = TransitionQuery::create()
            ->setConditions([
                ['subject_type', '=', get_class($this)],
            ])->setUntil($givenTime)
            ->setTake(1);

        $transitionRepository = app(TransitionRepository::class)->find($query);

        /** @var PersistedTransitionRecordInterface $state */
        $state = $transitionRepository->current();

        if (! $state or $state->getTransition()->getType() == 'model-deleted') {
            return null;  // model not exists
        } else {
            return $state->getTransition()->getModelCreatedFromState($this);
        }
    }

    /**
     * @param bool $exists
     * @return $this
     */
    public function setExists($exists = true)
    {
        $this->exists = $exists;

        return $this;
    }
}
