<?php

namespace Debuqer\EloquentMemory;

use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StateRememberObserver
{
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
        // $model->syncOriginal();
        ModelUpdated::createFromModel($model)->persist();
    }

    public function deleted(Model $model): void
    {
        if (! in_array(SoftDeletes::class, class_uses($model)) or $model->isForceDeleting()) {
            ModelDeleted::createFromModel($model)->persist();
        } else {
            $model->syncOriginal();
            ModelUpdated::createFromModel($model)->persist();
        }
    }
}
