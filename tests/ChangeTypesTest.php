<?php
use \Debuqer\EloquentMemory\Tests\Example\Post;
use \Debuqer\EloquentMemory\Change;
use \Illuminate\Database\Eloquent\Factories\Factory;

it('creates a model and check change detected as create', function () {
    $new = Factory::factoryForModel(Post::class)->createOne();
    $old = null;
    $change = new Change($old, $new);

    \PHPUnit\Framework\assertEquals('create', $change->getType());
});

it('creates a model and check apply will create the model', function () {
    $new = Factory::factoryForModel(Post::class)->createOne();
    $old = null;
    $change = new Change($old, $new);

    // remove the model to check if the change can create it again or not
    $new->delete();

    $change->apply();
    $newModelAfterCreation = Post::find($new->id);

    \PHPUnit\Framework\assertNotNull($newModelAfterCreation);
    \PHPUnit\Framework\assertEquals($newModelAfterCreation->getKey(), $new->getKey());
});

it('creates a model and check if rollback can remove the model', function () {
    $new = Factory::factoryForModel(Post::class)->createOne();
    $old = null;
    $change = new Change($old, $new);

    $change->rollback();

    $newModelAfterRemove = Post::find($new->getKey());
    \PHPUnit\Framework\assertNull($newModelAfterRemove);
});

it('create a model and check multiple of rollback and apply works', function () {
    $new = Factory::factoryForModel(Post::class)->createOne();
    $old = null;
    $change = new Change($old, $new);

    $change->rollback(); // remove the new model
    \PHPUnit\Framework\assertNull(Post::find($new->getKey()));
    $change->apply(); // create the new model
    \PHPUnit\Framework\assertNotNull(Post::find($new->getKey()));
    $change->rollback(); // remove the new model
    \PHPUnit\Framework\assertNull(Post::find($new->getKey()));
    $change->apply(); // create the new model
    \PHPUnit\Framework\assertNotNull(Post::find($new->getKey()));
});
