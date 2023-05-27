<?php
use Debuqer\EloquentMemory\Transitions\ModelRestored;
use Debuqer\EloquentMemory\Transitions\ModelSoftDeleted;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;

beforeEach(function () {
    $softDeletableModelClass = new class extends \Debuqer\EloquentMemory\Tests\Fixtures\Post {
        use \Illuminate\Database\Eloquent\SoftDeletes;

        protected $table = 'posts';

        protected $casts = [
            'meta' => 'json',
        ];
    };

    $attributes = $this->createAFakePost()->getRawOriginal();
    $softDeletableModelClass->setRawAttributes($attributes)->save();
    $before = $softDeletableModelClass::first();
    $before->delete();
    $after = (clone $before);
    $after->restore();

    $this->c = ModelRestored::createFromModel($before, $after);
    $this->before = $before;
    $this->after = $after;
});

/**
 * @deprecated
 */
test('up will restore a model from database', function () {
    $this->c->up();

    expect($this->after->refresh()->trashed())->toBeFalse();
});

/**
 * @deprecated
 */
test('getRollbackChange will return instance of ModelSoftDeleted with same properties', function () {
    expect($this->c->getRollbackChange())->toBeInstanceOf(ModelSoftDeleted::class);
    expect($this->c->getRollbackChange()->getModelKey())->toBe($this->c->getModelKey());
    expect($this->arraysAreTheSame($this->c->getRollbackChange()->getOldAttributes(), $this->c->getAttributes()))->toBeTrue();
    expect($this->arraysAreTheSame($this->c->getRollbackChange()->getAttributes(), $this->c->getOldAttributes()))->toBeTrue();
});

test('can persist in db', function () {
    $this->c->persist();

    expect($this->c->getModel())->not->toBeNull();
    expect($this->c->getModel()->properties)->toBe($this->c->getProperties());
    expect($this->c->getModel()->properties['old'])->toBe($this->before->getRawOriginal());
    expect($this->c->getModel()->properties['attributes'])->toBe($this->after->getRawOriginal());
    expect($this->c->getModel()->properties['key'])->toBe($this->after->getKey());
    expect($this->c->getModel()->properties['model_class'])->toBe(get_class($this->after));
    expect($this->c->getModel()->type)->toBe('model-restored');
});

test('raise error when model not exists at all', function () {
    $this->after->forceDelete();

    $this->c->up();
})->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

test('created from persisted record can migrate up and down', function () {
    $this->c->persist();
    $persist = $this->c->getModel();

    $c = ModelRestored::createFromPersistedRecord($persist);
    $c->up();
    $post = Post::first();
    expect($post->getRawOriginal('deleted_at'))->toBe($this->after->getRawOriginal('deleted_at'));
    $c->down();
    $post = Post::first();
    expect($post->getRawOriginal('deleted_at'))->toBe($this->before->getRawOriginal('deleted_at'));
});
