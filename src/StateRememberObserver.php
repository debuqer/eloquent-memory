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
        $newModel = $model->fresh();
        $newModel->syncChanges();
        $newModel->syncOriginal();
        ModelUpdated::createFromModel($newModel)->persist();
    }

    public function deleted(Model $model): void
    {
        $newModel = $model->refresh();
        if (! in_array(SoftDeletes::class, class_uses($newModel)) or $newModel->isForceDeleting()) {
            ModelDeleted::createFromModel($newModel)->persist();
        } else {
            $newModel->syncOriginal();
            ModelUpdated::createFromModel($newModel)->persist();
        }
    }
}
