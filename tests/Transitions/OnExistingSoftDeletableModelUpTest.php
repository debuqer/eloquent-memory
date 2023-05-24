<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithSoftDelete;
use \Debuqer\EloquentMemory\Transitions\ModelCreated;
use \Debuqer\EloquentMemory\Transitions\ModelDeleted;

beforeEach(function () {
    $this->model = $this->createAPost(PostWithSoftDelete::class);
    $this->attributes = $this->model->getRawOriginal();

    $this->transitions = [
        'ModelCreated' => ModelCreated::createFromModel($this->model),
        'ModelDeleted' => ModelDeleted::createFromModel($this->model),
    ];
});

test('[ModelDeleted] can delete the model even it uses soft deleting', function () {
    $this->transitions['ModelDeleted']->up();

    PostWithSoftDelete::withTrashed()->findOrFail($this->model->getKey());
})->expectException(ModelNotFoundException::class);

test('[ModelDeleted] can delete already soft deleted model', function () {
    $this->model->delete();
    $this->transitions['ModelDeleted']->up();

    PostWithSoftDelete::withTrashed()->findOrFail($this->model->getKey());
})->expectException(ModelNotFoundException::class);
