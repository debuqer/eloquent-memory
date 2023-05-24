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
        'ModelCreated' => ModelCreated::createFromModel($this->model),
        'ModelDeleted' => ModelDeleted::createFromModel($this->model),
    ];
});




it('[ModelCreated] has correct rollbackTransition', function () {
    expect($this->transitions['ModelCreated']->getRollbackChange())->toBeInstanceOf(ModelDeleted::class);
    expect($this->transitions['ModelCreated']->getRollbackChange()->getOldAttributes())->toBe($this->model->getRawOriginal());
});

it('[ModelDeleted] has correct rollbackTransition', function () {
    expect($this->transitions['ModelDeleted']->getRollbackChange())->toBeInstanceOf(ModelCreated::class);
    expect($this->transitions['ModelDeleted']->getRollbackChange()->getAttributes())->toBe($this->model->getRawOriginal());
});

