<?php

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


    foreach ($this->transitions as $transition) {
        $transition->persist();
    }
});

it('[ModelCreated] can persist', function () {
    expect($this->transitions['ModelCreated']->getModel())->not->toBeNull();
});

it('[ModelCreated] can be made from persisted record', function () {
    $persistedTransition = ModelCreated::createFromPersistedRecord($this->transitions['ModelCreated']->getModel());

    expect($persistedTransition->getType())->toBe($this->transitions['ModelCreated']->getType());
    expect($persistedTransition->getModelClass())->toBe($this->transitions['ModelCreated']->getModelClass());
    expect($persistedTransition->getProperties())->toBe($this->transitions['ModelCreated']->getProperties());
    expect($persistedTransition->getRollbackChange()->getProperties())->toBe($this->transitions['ModelCreated']->getRollbackChange()->getProperties());
});

it('[ModelCreated] can persist without considering mutators', function () {
    $persistedTransition = ModelCreated::createFromPersistedRecord($this->transitions['ModelCreated']->getModel());

    expect($persistedTransition)->not->toBeNull();
    expect($persistedTransition->getAttributes()['title'])->not->toBe('This title has changed');
});

it('[ModelDeleted] can persist', function () {
    expect($this->transitions['ModelDeleted']->getModel())->not->toBeNull();
});


it('[ModelDeleted] can be made from persisted record', function () {
    $persistedTransition = ModelDeleted::createFromPersistedRecord($this->transitions['ModelDeleted']->getModel());

    expect($persistedTransition->getType())->toBe($this->transitions['ModelDeleted']->getType());
    expect($persistedTransition->getModelClass())->toBe($this->transitions['ModelDeleted']->getModelClass());
    expect($persistedTransition->getProperties())->toBe($this->transitions['ModelDeleted']->getProperties());
    expect($persistedTransition->getRollbackChange()->getProperties())->toBe($this->transitions['ModelDeleted']->getRollbackChange()->getProperties());
});

it('[ModelDeleted] retrieved persisted record can migrate.up() and migrate.down()', function () {
    $persistedTransition = ModelDeleted::createFromPersistedRecord($this->transitions['ModelDeleted']->getModel());

    $persistedTransition->up();
    expect(Post::find($this->model->getKey()))->toBeNull();
    $persistedTransition->down();
    expect(Post::find($this->model->getKey()))->not->toBeNull();
});

it('[ModelDeleted] can persist without considering mutators', function () {
    $persistedTransition = ModelDeleted::createFromPersistedRecord($this->transitions['ModelDeleted']->getModel());

    expect($persistedTransition)->not->toBeNull();
    expect($persistedTransition->getOldAttributes()['title'])->not->toBe('This title has changed');
});
