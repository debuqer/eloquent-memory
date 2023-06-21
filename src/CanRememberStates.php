<?php

namespace Debuqer\EloquentMemory;

use Carbon\Carbon;
use Debuqer\EloquentMemory\Repositories\Eloquent\EloquentTransitionPersistDriver;
use Debuqer\EloquentMemory\Repositories\PersistedTransitionRecordInterface;
use Debuqer\EloquentMemory\Repositories\TransitionRepository;

trait CanRememberStates
{
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
        return md5(get_class($this) . serialize($this->getKey()));
    }

    /**
     * @param Carbon $givenTime
     * @return null
     */
    public function getStateOf(Carbon $givenTime)
    {
        $transitionRepository = app(TransitionRepository::class)->find([
            'conditions' => [
                ['subject_type', '=', get_class($this)],
            ],
            'until' => $givenTime,
            'limit' => 1,
        ]);

        /** @var PersistedTransitionRecordInterface $state */
        $state = $transitionRepository->current();

        if (! $state or $state->getTransition()->getType() == 'model-deleted') {
            return null;  // model not exists
        } else {
            return $state->getTransition()->getModelCreatedFromState();
        }
    }
}
