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

it('creates a model and check multiple of rollback and apply works', function () {
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

it('removes the model and check it can be recognized as delete', function () {
    $old = Factory::factoryForModel(Post::class)->createOne();
    $new = null;
    $change = new Change($old, $new);

    \PHPUnit\Framework\assertEquals('delete', $change->getType());
});

it('removes the model and check if apply can remove the model', function () {
    $old = Factory::factoryForModel(Post::class)->createOne();
    $new = null;
    $change = new Change($old, $new);


    $change->apply(); // should remove the model
    $modelAfterRemove = Post::find($old->getKey());
    \PHPUnit\Framework\assertNull($modelAfterRemove);
});

it('remove the model and check if rollback will create the model', function () {
    $old = Factory::factoryForModel(Post::class)->createOne();
    $old->refresh();
    $new = null;
    $change = new Change($old, $new);

    $change->apply(); // remove the model
    $change->rollback(); // should create the model

    $modelAfterDeleteRollback = Post::find($old->getKey());
    \PHPUnit\Framework\assertNotNull($modelAfterDeleteRollback);
    \PHPUnit\Framework\assertEquals($modelAfterDeleteRollback->getKey(), $old->getKey());
    \PHPUnit\Framework\assertEquals($old->getAttributes(), $modelAfterDeleteRollback->getAttributes());
});

it('update a model and check if update can be detected', function () {
    $old = Factory::factoryForModel(Post::class)->createOne();
    $new = clone $old;
    $new->update([
        'title' => \Faker\Factory::create('en')->name,
    ]);

    $change = new Change($old, $new);
    \PHPUnit\Framework\assertEquals('update', $change->getType());
});

it('update a model and check if updated properly', function() {
    $old = Factory::factoryForModel(Post::class)->createOne();
    $new = clone $old;
    $new->title = \Faker\Factory::create('en')->name;

    $change = new Change($old, $new);

    $change->apply();
    $modelAfterUpdate = Post::find($new->getKey());
    \PHPUnit\Framework\assertEquals($new->title, $modelAfterUpdate->title);
});
