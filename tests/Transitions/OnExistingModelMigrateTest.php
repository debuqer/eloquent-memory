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


it('[ModelCreated] migrate.down() removes recently created model', function () {
    $this->transitions['ModelCreated']->down();

    Post::findOrFail($this->model->getKey());
})->expectException(ModelNotFoundException::class);

it('[ModelDeleted] migrate.up() will forceDelete the model from database', function () {
    $this->transitions['ModelDeleted']->up();

    Post::findOrFail($this->model->getKey());
})->expectException(ModelNotFoundException::class);


it('[ModelDeleted] migrate.up(), migrate.down() and migrate.up() works', function () {
    $this->transitions['ModelDeleted']->up();
    expect(Post::find($this->model->getKey()))->toBeNull();

    $this->transitions['ModelDeleted']->down();
    expect(Post::find($this->model->getKey()))->not->toBeNull();

    $this->transitions['ModelDeleted']->up();
    expect(Post::find($this->model->getKey()))->toBeNull();
});

it('[ModelDeleted] migrate.up() and migrate.up() doesnt work', function () {
    $this->transitions['ModelDeleted']->up();
    expect(Post::find($this->model->getKey()))->toBeNull();

    $this->transitions['ModelDeleted']->up();
    expect(Post::find($this->model->getKey()))->toBeNull();
})->expectException(ModelNotFoundException::class);

