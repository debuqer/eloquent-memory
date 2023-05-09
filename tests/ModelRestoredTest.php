<?php
use Debuqer\EloquentMemory\ChangeTypes\ModelRestored;
use Debuqer\EloquentMemory\ChangeTypes\ModelSoftDeleted;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;

beforeEach(function () {
    $softDeletableModelClass = new class extends \Debuqer\EloquentMemory\Tests\Fixtures\Post {
        use \Illuminate\Database\Eloquent\SoftDeletes;

        protected $table = 'posts';

        protected $casts = [
            'meta' => 'json',
        ];
    };

    $attributes = createAFakePost()->getRawOriginal();
    $softDeletableModelClass->setRawAttributes($attributes)->save();
    $before = $softDeletableModelClass::first();
    $before->delete();
    $after = (clone $before);
    $after->restore();

    $this->c = ModelRestored::createFromModel($before, $after);
    $this->before = $before;
    $this->after = $after;
});

test('up will restore a model from database', function () {
    $this->c->up();

    expect($this->after->refresh()->trashed())->toBeFalse();
});

test('getRollbackChange will return instance of ModelSoftDeleted with same properties', function () {
    expect($this->c->getRollbackChange())->toBeInstanceOf(ModelSoftDeleted::class);
    expect($this->c->getRollbackChange()->getModelKey())->toBe($this->c->getModelKey());
    testAttributes($this->c->getRollbackChange()->getOldAttributes(), $this->c->getAttributes());
    testAttributes($this->c->getRollbackChange()->getAttributes(), $this->c->getOldAttributes());
});

test('can persist in db', function () {
    $this->c->persist();

    expect($this->c->getModel())->not->toBeNull();
    expect($this->c->getModel()->parameters)->toBe($this->c->getParameters());
    expect($this->c->getModel()->parameters['old'])->toBe($this->before->getRawOriginal());
    expect($this->c->getModel()->parameters['attributes'])->toBe($this->after->getRawOriginal());
    expect($this->c->getModel()->parameters['key'])->toBe($this->after->getKey());
    expect($this->c->getModel()->parameters['model_class'])->toBe(get_class($this->after));
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
