<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithSoftDelete;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Transitions\ModelRestored;
use Debuqer\EloquentMemory\Transitions\ModelSoftDeleted;

beforeEach(function () {
    $this->model = $this->createAPost(PostWithSoftDelete::class);
    $this->attributes = $this->model->getRawOriginal();


    $this->deleteAttributeChanges = [
        'deleted_at' => \Carbon\Carbon::now(),
    ];
    $this->restoreAttributeChanges = [
        'deleted_at' => null,
    ];

    $this->transitions = [
        'ModelCreated' => ModelCreated::createFromModel($this->model),
        'ModelDeleted' => ModelDeleted::createFromModel($this->model),
    ];
});

it('[ModelDeleted] can delete the model even it uses soft deleting', function () {
    $this->transitions['ModelDeleted']->up();

    PostWithSoftDelete::withTrashed()->findOrFail($this->model->getKey());
})->expectException(ModelNotFoundException::class);

it('[ModelDeleted] can delete already soft deleted model', function () {
    $this->model->delete();
    $this->transitions['ModelDeleted']->up();

    PostWithSoftDelete::withTrashed()->findOrFail($this->model->getKey());
})->expectException(ModelNotFoundException::class);

it('[ModelRestored] migrate.up() can restore the model', function () {
    $this->model->delete();
    $transition = ModelRestored::createFromChanges($this->model, $this->restoreAttributeChanges); // restore transition
    $transition->up();

    expect(PostWithSoftDelete::withTrashed()->findOrFail($this->model->getKey()))->not->toBeNull();
});

it('[ModelRestored] has correct rollbackTransition', function () {
    $this->model->delete();
    $transition = ModelRestored::createFromChanges($this->model, $this->restoreAttributeChanges); // restore transition

    expect($transition->getRollbackChange())->toBeInstanceOf(ModelSoftDeleted::class);
    expect($transition->getRollbackChange()->getModelKey())->toBe($this->model->getKey());
    expect($transition->getRollbackChange()->getAttributes())->toBe($this->model->getRawOriginal());
});
