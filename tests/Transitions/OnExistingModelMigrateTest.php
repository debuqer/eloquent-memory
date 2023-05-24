<?php
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use \Debuqer\EloquentMemory\Transitions\ModelCreated;
use \Debuqer\EloquentMemory\Transitions\ModelDeleted;

beforeEach(function () {
    $this->model = $this->createAPost();
    $this->attributes = $this->model->getRawOriginal();

    $this->transitions = [
        'ModelCreated' => ModelCreated::createFromModel($this->model),
        'ModelDeleted' => ModelDeleted::createFromModel($this->model),
    ];
});

it('[ModelCreated] migrate.up() can not re-create the model', function () {
    $this->transitions['ModelCreated']->up();
    $this->transitions['ModelCreated']->up();
})->expectException(QueryException::class);

it('[ModelDeleted] migrate.up() will forceDelete the model from database', function () {
    $this->transitions['ModelDeleted']->up();

    Post::findOrFail($this->model->getKey());
})->expectException(ModelNotFoundException::class);

