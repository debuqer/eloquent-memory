<?php
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Illuminate\Database\QueryException;
use \Debuqer\EloquentMemory\Transitions\ModelCreated;
use \Debuqer\EloquentMemory\Transitions\ModelDeleted;

beforeEach(function () {
    $this->model = $this->createAFakePost();
    $this->attributes = $this->model->getRawOriginal();

    $this->transitions = [
        'ModelCreated' => ModelCreated::createFromModel($this->model),
        'ModelDeleted' => ModelDeleted::createFromModel($this->model),
    ];
});




it('[ModelCreated] migrate.up() can re-create the model', function () {
    $this->transitions['ModelCreated']->up();

    expect($this->model->exists)->toBeTrue();
    expect($this->arraysAreTheSame($this->model->getRawOriginal(), $this->attributes))->toBeTrue();
});


it('[ModelCreated] migrate.up() can re-create the model without changing created_at and updated_at', function () {
    Carbon::setTestNow(Carbon::now()->addHour()); // traveling in time
    $this->transitions['ModelCreated']->up();

    $post = Post::first(); // get the post directly from database

    expect($post->created_at->toString())->toBe($this->model->created_at->toString());
    expect($post->updated_at->toString())->toBe($this->model->updated_at->toString());
});


it('[ModelCreated] migrate.up() can not re-create another model when id reserved', function () {
    $this->createAPost(); // reserves id = 1

    $this->transitions['ModelCreated']->up();
})->expectException(QueryException::class);


it('[ModelCreated] migrate.up() will fill guarded fields too', function () {
    $this->transitions['ModelCreated']->up();

    $recentlyReCreatedModel = Post::first();
    expect($recentlyReCreatedModel->getKey())->toBe($this->model->getKey());
});


it('[ModelDeleted] migrate.down() can re-create the model', function () {
    $this->transitions['ModelDeleted']->down();

    $recentlyReCreatedModel = Post::first();
    expect($recentlyReCreatedModel->getKey())->toBe($this->model->getKey());
    expect($this->arraysAreTheSame($recentlyReCreatedModel->getAttributes(), $this->model->getAttributes()))->toBeTrue();
    expect($this->arraysAreTheSame($recentlyReCreatedModel->getRawOriginal(), $this->model->getRawOriginal()))->toBeTrue();
});

it('[ModelDeleted] migrate.down() and migrate.down() doesnt work', function () {
    $this->transitions['ModelDeleted']->down();
    expect(Post::find($this->model->getKey()))->not->toBeNull();

    $this->transitions['ModelDeleted']->down();
    expect(Post::find($this->model->getKey()))->not->toBeNull();
})->expectException(QueryException::class);


it('[ModelDeleted] migrate.up() doesnt work when model already deleted', function () {
    $this->model->forceDelete();

    $this->transitions['ModelDeleted']->up();
    expect(Post::find($this->model->getKey()))->not->toBeNull();
})->expectException(ModelNotFoundException::class);
