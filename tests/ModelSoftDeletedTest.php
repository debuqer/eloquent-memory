<?php
use Debuqer\EloquentMemory\Transitions\ModelSoftDeleted;
use Debuqer\EloquentMemory\Transitions\ModelRestored;
use Debuqer\EloquentMemory\Tests\Fixtures\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;

beforeEach(function () {
    $softDeletableModelClass = new class extends \Debuqer\EloquentMemory\Tests\Fixtures\Post
    {
        use \Illuminate\Database\Eloquent\SoftDeletes;

        protected $table = 'posts';

        protected $casts = [
            'meta' => 'json',
        ];
    };
    $this->softDeletableModelClass = $softDeletableModelClass;

    $attributes = $this->createAFakePost()->getRawOriginal();
    $softDeletableModelClass->setRawAttributes($attributes)->save();
    $before = $softDeletableModelClass::first();
    $after = (clone $before);
    $after->delete();

    $this->before = (clone $before);
    $this->after = (clone $after);

    $this->c = ModelSoftDeleted::createFromModel($before, $after);
    $after->restore();
});

/**
 * ModelSoftDeleted
 */
test('up will soft delete a model from database', function () {
    $this->c->up();

    expect($this->after->refresh()->trashed())->toBeTrue();
});


/**
 * ModelSoftDeleted Rollback
 */
test('getRollbackChange will return instance of ModelRestored with same properties', function () {
    expect($this->c->getRollbackChange())->toBeInstanceOf(ModelRestored::class);
    expect($this->c->getRollbackChange()->getModelKey())->toBe($this->c->getModelKey());
    expect($this->arraysAreTheSame($this->c->getRollbackChange()->getOldAttributes(), $this->c->getAttributes()))->toBeTrue();
    expect($this->arraysAreTheSame($this->c->getRollbackChange()->getAttributes(), $this->c->getOldAttributes()))->toBeTrue();
});

test('raise error when model not using softDelete', function() {
    $before = $this->createAPost();
    $after = (clone $before);
    $after->delete();

    $c = ModelSoftDeleted::createFromModel($before, $after);
    $c->up();
})->expectException(ModelNotFoundException::class);

test('can persist in db', function () {
    $this->c->persist();

    expect($this->c->getModel())->not->toBeNull();
    expect($this->c->getModel()->properties)->toBe($this->c->getProperties());
    expect($this->c->getModel()->properties['old'])->toBe($this->before->getRawOriginal());
    expect($this->c->getModel()->properties['attributes'])->toBe($this->after->getRawOriginal());
    expect($this->c->getModel()->properties['key'])->toBe($this->after->getKey());
    expect($this->c->getModel()->properties['model_class'])->toBe(get_class($this->after));
    expect($this->c->getModel()->type)->toBe('model-soft-deleted');
});


test('raise error when model not exists at all', function () {
    $this->after->forceDelete();

    $this->c->up();
})->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

