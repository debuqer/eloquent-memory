<?php
use Debuqer\EloquentMemory\ChangeTypes\ModelSoftDeleted;
use Debuqer\EloquentMemory\ChangeTypes\ModelRestored;
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

    $attributes = createAFakePost()->getRawOriginal();
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
    testAttributes($this->c->getRollbackChange()->getOldAttributes(), $this->c->getAttributes());
    testAttributes($this->c->getRollbackChange()->getAttributes(), $this->c->getOldAttributes());
});

test('raise error when model not using softDelete', function() {
    $before = createAPost();
    $after = (clone $before);
    $after->delete();

    $c = ModelSoftDeleted::createFromModel($before, $after);
    $c->up();
})->expectException(ModelNotFoundException::class);

test('can persist in db', function () {
    $this->c->persist();

    expect($this->c->getModel())->not->toBeNull();
    expect($this->c->getModel()->parameters)->toBe($this->c->getParameters());
    expect($this->c->getModel()->parameters['old'])->toBe($this->before->getRawOriginal());
    expect($this->c->getModel()->parameters['attributes'])->toBe($this->after->getRawOriginal());
    expect($this->c->getModel()->parameters['key'])->toBe($this->after->getKey());
    expect($this->c->getModel()->parameters['model_class'])->toBe(get_class($this->after));
    expect($this->c->getModel()->type)->toBe('model-soft-deleted');
});


test('raise error when model not exists at all', function () {
    $this->after->forceDelete();

    $this->c->up();
})->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

