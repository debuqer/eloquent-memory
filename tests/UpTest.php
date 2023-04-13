<?php
use \Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use \Debuqer\EloquentMemory\ChangeTypes\ModelDeleted;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;

/**
 * ModelCreated
 */
test('ModelCreated up will add a model with same properties', function () {
    $item = createAFakePost();
    $c = new ModelCreated(get_class($item), $item->getAttributes());
    $c->up();

    $item->refresh();
    expect($item->exists)->toBeTrue();
});


/**
 * ModelDeleted
 */
test('ModelDeleted up will remove a model from database', function () {
    $item = createAPost();
    $c = new ModelDeleted(get_class($item), $item->getAttributes());
    $c->up();

    expect(Post::find($item->getKey()))->toBeNull();
});
