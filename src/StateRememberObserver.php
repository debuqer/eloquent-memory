<?php


namespace Debuqer\EloquentMemory;


use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Transitions\ModelRestored;
use Debuqer\EloquentMemory\Transitions\ModelSoftDeleted;
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
        if ( $this->hasState('updating', $model) ) {
            ModelUpdated::createFromModel($this->getState('updating', $model), $model)->persist();
            $this->unsetState('updating', $model);
        }
    }

    public function updating(Model $model): void
    {
        $this->setState('updating', $model);
    }


    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        if ( in_array(SoftDeletes::class, class_uses($model)) and $model->trashed() ) {
            ModelSoftDeleted::createFromModel($this->getState('deleting', $model), $model)->persist();
        } else {
            ModelDeleted::createFromModel($model)->persist();
        }
    }


    public function deleting(Model $model)
    {
        $this->setState('deleting', (clone $model));
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function trashed(Model $model): void
    {
        if ( $this->hasState('deleting', $model) ) {
            ModelSoftDeleted::createFromModel($this->getState('deleting', $model), $model)->persist();
            $this->unsetState('deleting', $model);
        }
    }


    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        if ( $this->hasState('restoring', $model) ) {
            ModelRestored::createFromModel($this->getState('restoring', $model), $model)->persist();
            $this->unsetState('restoring', $model);
        }
    }

    public function restoring(Model $model): void
    {
        $this->setState('restoring', (clone $model));
    }



    private function setState($event, $model)
    {
        static::$map[$model->getModelHash()][$event] = (clone $model);
    }

    private function unsetState($event, $model)
    {
        unset(static::$map[$model->getModelHash()][$event]);
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
