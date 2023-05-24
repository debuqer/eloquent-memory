<?php

use \Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;

beforeEach(function () {
    $this->model = $this->createAPost();
    $this->attributes = $this->model->getRawOriginal();

    $this->transitions = [
        'ModelCreated' => ModelCreated::createFromModel($this->model)
    ];


    foreach ($this->transitions as $transition) {
        $transition->persist();
    }
});

it('[ModelCreated] can persist', function () {
    expect($this->transitions['ModelCreated']->getModel())->not->toBeNull();
});

it('[ModelCreated] can be made by persisted record', function () {
    $persistedTransition = ModelCreated::createFromPersistedRecord($this->transitions['ModelCreated']->getModel());

    expect($persistedTransition->getType())->toBe($this->transitions['ModelCreated']->getType());
    expect($persistedTransition->getModelClass())->toBe($this->transitions['ModelCreated']->getModelClass());
    expect($persistedTransition->getProperties())->toBe($this->transitions['ModelCreated']->getProperties());
    expect($persistedTransition->getRollbackChange()->getProperties())->toBe($this->transitions['ModelCreated']->getRollbackChange()->getProperties());
});

it('[ModelCreated] can be made by persisted record and migrate up, down and up!', function () {
    $this->model->forceDelete(); // to test migrate.up()
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
