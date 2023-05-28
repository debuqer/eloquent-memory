<?php


namespace Debuqer\EloquentMemory;


use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Transitions\ModelRestored;
use Debuqer\EloquentMemory\Transitions\ModelSoftDeleted;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;
use Illuminate\Database\Eloquent\Model;
trait CanRememberStates
{
    public static function booted()
    {
        static::created(function(Model $model) {
            ModelCreated::createFromModel($model->fresh())->persist();
        });

        static::updated(function(Model $model)  {
            if (method_exists(static::class, 'bootSoftDeletes')) {
                $changes = $model->getChanges();
                if ( array_key_exists($model->getDeletedAtColumn(), $changes)) {
                    if ( ! $changes[$model->getDeletedAtColumn()]) {
                        /** @var Model $model */
                        $attributesBeforeChange = collect($model->getRawOriginal())->only(array_keys($model->getChanges()))->toArray();
                        $attributesAfterChange = $model->getChanges();

                        (new ModelRestored([
                            'model_class' => get_class($model),
                            'key' => $model->getKey(),
                            'old' => $attributesBeforeChange,
                            'attributes' => $attributesAfterChange
                        ]))->persist();
                    }
                }
            }
            else {
                $attributesBeforeChange = $model->getRawOriginal();
                $attributesAfterChange = array_merge($model->getRawOriginal(), $model->getChanges());
                $newModel = $model->fresh();

                (new ModelUpdated([
                    'model_class' => get_class($newModel),
                    'key' => $newModel->getKey(),
                    'old' => $attributesBeforeChange,
                    'attributes' => $attributesAfterChange
                ]))->persist();
            }
        });

        if ( ! method_exists(static::class, 'bootSoftDeletes')) {
            static::deleted(function (Model $model) {
                ModelDeleted::createFromModel($model)->persist();
            });
        } else {
            static::softDeleted(function (Model $model) {
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

        }
    }
}
