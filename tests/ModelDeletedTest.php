<?php
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\ChangeTypes\ModelDeleted;


test('ModelDeleted::up will forceDelete a model from database', function () {
    $item = createAPost();
    $c = new ModelDeleted(get_class($item), $item->getRawOriginal());
    $c->up();

    expect(Post::find($item->getKey()))->toBeNull();
});

test('ModelDeleted::getRollbackChange returns instance of ModelCreated with same properties ', function () {
    $item = createAPost();
    $c = new ModelDeleted(get_class($item), $item->getRawOriginal());

    expect($c->getRollbackChange()->getType())->toBe('model-created');
    expect($c->getRollbackChange()->getAttributes())->toBe($item->getRawOriginal());
});

test('ModelDeleted::down will create a model with same properties', function () {
    // test not created
});
