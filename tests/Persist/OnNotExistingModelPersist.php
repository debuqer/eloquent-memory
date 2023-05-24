<?php

use \Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;

beforeEach(function () {
    $this->model = $this->createAFakePost();
    $this->attributes = $this->model->getRawOriginal();

    $this->transitions = [
        'ModelCreated' => ModelCreated::createFromModel($this->model)
    ];


    foreach ($this->transitions as $transition) {
        $transition->persist();
    }

    $this->model->forceDelete(); // to test migrate.up()
});

it('[ModelCreated] can be made by persisted record and migrate up, down and up!', function () {
    $persistedTransition = ModelCreated::createFromPersistedRecord($this->transitions['ModelCreated']->getModel());

    $persistedTransition->up();
    expect(Post::find($this->model->getKey()))->not->toBeNull();

    $persistedTransition->down();
    expect(Post::find($this->model->getKey()))->toBeNull();

    $persistedTransition->up();
    expect(Post::find($this->model->getKey()))->not->toBeNull();
    expect($this->arraysAreTheSame(Post::find($this->model->getKey())->getRawOriginal(), $this->model->getRawOriginal()))->toBeTrue();
    expect($this->arraysAreTheSame(Post::find($this->model->getKey())->getAttributes(), $this->model->getAttributes()))->toBeTrue();
});
