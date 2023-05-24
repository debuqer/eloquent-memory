<?php
use Carbon\Carbon;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
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




it('[ModelCreated] has correct rollbackTransition', function () {
    expect($this->transitions['ModelCreated']->getRollbackChange())->toBeInstanceOf(ModelDeleted::class);
    expect($this->transitions['ModelCreated']->getRollbackChange()->getOldAttributes())->toBe($this->model->getRawOriginal());
});



it('[ModelCreated] can re-create the model without changing created_at and updated_at', function () {
    Carbon::setTestNow(Carbon::now()->addHour()); // traveling in time
    $this->transitions['ModelCreated']->up();

    $post = Post::first(); // get the post directly from database

    expect($post->created_at->toString())->toBe($this->model->created_at->toString());
    expect($post->updated_at->toString())->toBe($this->model->updated_at->toString());
});
