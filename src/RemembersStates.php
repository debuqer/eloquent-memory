<?php


namespace Debuqer\EloquentMemory;


use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;
use Illuminate\Database\Eloquent\Model;

trait RemembersStates
{
    public static function boot()
    {
        parent::boot();

        static::created(function(Model $model) {
            ModelCreated::createFromModel($model->fresh())->persist();
        });
        static::updated(function(Model $model)  {
            $attributesAfterChange = array_merge($model->getRawOriginal(), $model->getChanges());
            (new ModelUpdated([
                'model_class' => get_class($model),
                'key' => $model->getKey(),
                'old' => $model->getRawOriginal(),
                'attributes' => $attributesAfterChange
            ]))->persist();
        });
    }
}
