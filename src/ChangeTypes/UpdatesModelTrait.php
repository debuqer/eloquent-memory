<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Illuminate\Database\Eloquent\Model;

trait UpdatesModelTrait
{
    public function update(Model $source, Model $destination)
    {
        // some attributes may be miss in model object and this merge will prevent not updating them
        $allAttributes = array_keys(array_merge($source->getAttributes(), $destination->getAttributes()));

        $update = [];
        array_map(function ($attribute) use($source, $destination, &$update) {
            $update[$attribute] = $destination->getAttribute($attribute);
        }, $allAttributes);

        $source->getConnection()
            ->table($source->getTable())
            ->where($source->getKeyName(), $source->getKey())
            ->update($update);
    }
}
