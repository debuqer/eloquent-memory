<?php


namespace Debuqer\EloquentMemory;


use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StateRememberObserver
{
    protected static $map;

    /**
     * Handle the User "created" event.
     */
    public function created(Model $model): void
    {
        ModelCreated::createFromModel($model->fresh())->persist();
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $model->syncOriginal();
        ModelUpdated::createFromModel($this->getState('updating', $model), $model)->persist();

        $this->unsetState('updating', $model);
    }

    public function deleted(Model $model): void
    {
        if ( ! in_array(SoftDeletes::class, class_uses($model)) or $model->isForceDeleting()  ) {
            ModelDeleted::createFromModel($model)->persist();
        } else {
            $model->syncOriginal();
            ModelUpdated::createFromModel($this->getState('deleting', $model), $model)->persist();
        }

        $this->unsetState('deleting', $model);
    }

    public function deleting(Model $model): void
    {
        $this->setState('deleting', $model);
    }

    public function updating(Model $model): void
    {
        $this->setState('updating', $model);
    }


    private function setState($event, $model)
    {
        static::$map[$model->getModelHash()][$event] = (clone $model);
    }

    private function unsetState($event, $model)
    {
        if ( $this->hasState($event, $model) ) {
            unset(static::$map[$model->getModelHash()][$event]);
        }
    }

    private function hasState($event, $model)
    {
        return isset(static::$map[$model->getModelHash()]) and isset(static::$map[$model->getModelHash()][$event]);
    }

    private function getState($event, $model)
    {
        return $this->hasState($event, $model) ? static::$map[$model->getModelHash()][$event] : null;
    }
}
