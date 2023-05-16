<?php


namespace Debuqer\EloquentMemory;


use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Transitions\ModelSoftDeleted;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

trait RemembersStates
{
    public static function booted()
    {
        static::created(function(Model $model) {
            ModelCreated::createFromModel($model->fresh())->persist();
        });

        static::updated(function(Model $model)  {
            $attributesBeforeChange = $model->getRawOriginal();
            $attributesAfterChange = array_merge($model->getRawOriginal(), $model->getChanges());
            $newModel = $model->fresh();

            (new ModelUpdated([
                'model_class' => get_class($newModel),
                'key' => $newModel->getKey(),
                'old' => $attributesBeforeChange,
                'attributes' => $attributesAfterChange
            ]))->persist();
        });

        if (method_exists(static::class, 'bootSoftDeletes')) {
            static::softDeleted(function ($model) {
                /** @var Model $model */
                $attributesBeforeChange = [$model->getDeletedAtColumn() => null];
                $attributesAfterChange = [$model->getDeletedAtColumn() => $model->{$model->getDeletedAtColumn()}];
                $newModel = $model->fresh();

                (new ModelSoftDeleted([
                    'model_class' => get_class($newModel),
                    'key' => $newModel->getKey(),
                    'old' => $attributesBeforeChange,
                    'attributes' => $attributesAfterChange
                ]))->persist();
            });

            static::restored(function ($model) {
                //$model->broadcastRestored();
            });
        }
        else {
            static::deleted(function (Model $model) {
                ModelDeleted::createFromModel($model)->persist();
            });
        }
    }
}
