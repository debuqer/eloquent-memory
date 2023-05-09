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

        $beforeState = null;
        static::retrieved(function (Model $model) use(&$beforeState) {
            $beforeState = $model;
        });
        static::created(function(Model $model) {
            ModelCreated::createFromModel($model->refresh())->persist();
        });
        static::updated(function(Model $model) use($beforeState) {
            ModelUpdated::createFromModel($beforeState, $model->refresh())->persist();
        });
    }
}
