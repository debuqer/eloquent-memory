<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Concerns;


trait HasModelClass
{
    protected $modelClass;

    public function getModelClass()
    {
        return $this->modelClass;
    }
}
