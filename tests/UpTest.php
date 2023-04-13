<?php
use \Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
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
