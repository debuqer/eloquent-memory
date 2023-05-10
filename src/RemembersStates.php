<?php


namespace Debuqer\EloquentMemory;


use Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use Debuqer\EloquentMemory\ChangeTypes\ModelUpdated;
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
            (new ModelUpdated(get_class($model), $model->getKey(), $model->getRawOriginal(), $attributesAfterChange))->persist();
        });
    }
}
