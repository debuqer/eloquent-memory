<?php

use \Debuqer\EloquentMemory\Transitions\ModelCreated;

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
