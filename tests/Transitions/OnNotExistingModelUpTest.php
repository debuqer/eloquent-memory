<?php
use Carbon\Carbon;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Illuminate\Database\QueryException;
use \Debuqer\EloquentMemory\Transitions\ModelCreated;
use \Debuqer\EloquentMemory\Transitions\ModelDeleted;

beforeEach(function () {
    $this->model = $this->createAFakePost();
    $this->attributes = $this->model->getRawOriginal();

    $this->transitions = [
        'ModelCreated' => ModelCreated::createFromModel($this->model)
    ];
});




it('[ModelCreated] can re-create the model', function () {
    $this->transitions['ModelCreated']->up();

    expect($this->model->exists)->toBeTrue();
    expect($this->arraysAreTheSame($this->model->getRawOriginal(), $this->attributes))->toBeTrue();
});


it('[ModelCreated] can re-create the model without changing created_at and updated_at', function () {
    Carbon::setTestNow(Carbon::now()->addHour()); // traveling in time
    $this->transitions['ModelCreated']->up();

    $post = Post::first(); // get the post directly from database

    expect($post->created_at->toString())->toBe($this->model->created_at->toString());
    expect($post->updated_at->toString())->toBe($this->model->updated_at->toString());
});


it('[ModelCreated] can not re-create another model when id reserved', function () {
    $this->createAPost(); // reserves id = 1

    $this->transitions['ModelCreated']->up();
})->expectException(QueryException::class);


it('[ModelCreated] migrate.up() will fill guarded fields too', function () {
    $this->transitions['ModelCreated']->up();

    $recentlyReCreatedModel = Post::first();
    expect($recentlyReCreatedModel->getKey())->toBe($this->model->getKey());
});
