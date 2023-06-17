<?php


namespace Debuqer\EloquentMemory;


use Carbon\Carbon;
use Debuqer\EloquentMemory\StorageModels\ModelTransition;
use Debuqer\EloquentMemory\StorageModels\TransitionRepository;

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
            'subject_type' => get_class($this),
        ], null, $givenTime);

        /** @var ModelTransition $state */
        $state = $transitionRepository->current();

        if ( ! $state or $state->type === 'model-deleted' ) {
            return null;  // model not exists
        } else {
            return $state->getTransition()->getModelCreatedFromState();
        }
    }
}
