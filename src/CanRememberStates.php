<?php


namespace Debuqer\EloquentMemory;


use Carbon\Carbon;
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

    public function getStateOf(Carbon $givenTime)
    {
        $transitionRepository = app(TransitionRepository::class)->find([
            'subject_type' => get_class($this),
        ], null, $givenTime);

        $state = $transitionRepository->current();

        if ( ! $state ) {
            return null;  // model not exists
        } else {
            return (new $this)->forceFill($state['properties']['attributes'])->syncOriginal();
        }
    }
}
