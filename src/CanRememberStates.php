<?php


namespace Debuqer\EloquentMemory;


use Carbon\Carbon;
use Debuqer\EloquentMemory\StorageModels\TransitionRepository;

trait CanRememberStates
{
    public function getModelHash()
    {
        return md5($this->getTable().'_'.$this->getKeyName().'_'.$this->getKey());
    }

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
            'model_class' => get_class($this),
        ], null, $givenTime);

        $state = $transitionRepository->current();

        if ( ! $state ) {
            return null;  // model not exists
        } else {
            return (new $this)->forceFill($state['properties']['attributes'])->syncOriginal();
        }
    }
}
