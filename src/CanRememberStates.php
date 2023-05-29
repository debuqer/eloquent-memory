<?php


namespace Debuqer\EloquentMemory;


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
}
