<?php
use \Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use \Debuqer\EloquentMemory\ChangeTypes\ModelDeleted;
use \Debuqer\EloquentMemory\ChangeTypes\ModelSoftDeleted;
use \Debuqer\EloquentMemory\ChangeTypes\ModelUpdated;
use \Debuqer\EloquentMemory\ChangeTypes\ModelRestored;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;

/**
 * ModelCreated
 */
test('ModelCreated up will add a model with same properties', function () {
    $item = createAFakePost();
    $c = new ModelCreated(get_class($item), $item->getRawOriginal());
    $c->up();

    $item->refresh();
    expect($item->exists)->toBeTrue();
});


/**
 * ModelDeleted
 */
test('ModelDeleted up will remove a model from database', function () {
    $item = createAPost();
    $c = new ModelDeleted(get_class($item), $item->getRawOriginal());
    $c->up();

    expect(Post::find($item->getKey()))->toBeNull();
});

/**
 * ModelUpdated
 */
test('ModelUpdated up will update a model in database', function () {
    $after = createAPost();
    $before = (clone $after);
    $before->update([
        'title' => 'Title changed!'
    ]);

    $c = new ModelUpdated(get_class($after), $before->getRawOriginal(), $after->getRawOriginal());
    $c->up();

    expect(Post::find($after->getKey())->title)->toBe($after->title);
});

/**
 * ModelSoftDeleted
 */
test('ModelSoftDeleted up will soft delete a model in database', function () {
    $before = createAPost();
    $after = (clone $before);
    $after->delete();

    $c = new ModelSoftDeleted(get_class($after), $before->getRawOriginal(), $after->getRawOriginal());
    $c->up();

    expect($after->refresh()->trashed())->toBeTrue();
});


/**
 * ModelRestored
 */
test('ModelRestored up will restore a model in database', function () {
    $after = createAPost();
    $before = (clone $after);
    $before->delete();

    $c = new ModelRestored(get_class($after), $before->getRawOriginal(), $after->getRawOriginal());
    $c->up();

    expect($after->refresh()->trashed())->toBeFalse();
});

