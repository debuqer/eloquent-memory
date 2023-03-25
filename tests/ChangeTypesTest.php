<?php
use \Debuqer\EloquentMemory\Tests\Example\Post;
use \Debuqer\EloquentMemory\Change;
use \Illuminate\Database\Eloquent\Factories\Factory;

function createOnePost()
{
    return Factory::factoryForModel(Post::class)->createOne();
}

it('creates a model and check change detected as create', function () {
    $new = createOnePost();
    $old = null;
    $change = new Change($old, $new);

    \PHPUnit\Framework\assertEquals('create', $change->getType());
});

it('creates a model and check apply will create the model', function () {
    $new = createOnePost();
    $old = null;
    $change = new Change($old, $new);

    // remove the model to check if the change can create it again or not
    $new->forceDelete();

    $change->apply();
    $newModelAfterCreation = Post::find($new->id);

    \PHPUnit\Framework\assertNotNull($newModelAfterCreation);
    \PHPUnit\Framework\assertEquals($newModelAfterCreation->getKey(), $new->getKey());
});

it('creates a model and check if rollback can remove the model', function () {
    $new = createOnePost();
    $old = null;
    $change = new Change($old, $new);

    $change->rollback();

    $newModelAfterRemove = Post::find($new->getKey());
    \PHPUnit\Framework\assertNull($newModelAfterRemove);
});

it('creates a model and check multiple of rollback and apply works', function () {
    $new = createOnePost();
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
    $old = createOnePost();
    $new = null;
    $change = new Change($old, $new);

    \PHPUnit\Framework\assertEquals('delete', $change->getType());
});

it('removes the model and check if apply can remove the model', function () {
    $old = createOnePost();
    $new = null;
    $change = new Change($old, $new);


    $change->apply(); // should remove the model
    $modelAfterRemove = Post::find($old->getKey());
    \PHPUnit\Framework\assertNull($modelAfterRemove);
});

it('remove the model and check if rollback will create the model', function () {
    $old = createOnePost();
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
    $old = createOnePost();
    $new = clone $old;
    $new->update([
        'title' => \Faker\Factory::create('en')->name,
    ]);

    $change = new Change($old, $new);
    \PHPUnit\Framework\assertEquals('update', $change->getType());
});

it('update a model and check if updated properly', function() {
    $old = createOnePost();
    $new = clone $old;
    $new->title = \Faker\Factory::create('en')->name;

    $change = new Change($old, $new);

    $change->apply();
    $modelAfterUpdate = Post::find($new->getKey());
    \PHPUnit\Framework\assertEquals($new->title, $modelAfterUpdate->title);
});

it('soft delete can recognized properly', function () {
    $old = createOnePost();
    $new = (clone $old);
    $new->delete();

    $change = new Change($old, $new);

    \PHPUnit\Framework\assertEquals('softDelete', $change->getType());
});

it('soft delete can apply with right deleted_at column', function () {
    $now = \Carbon\Carbon::now()->toDateTimeString();

    $old = createOnePost();
    $new = (clone $old);
    $new->setAttribute($new->getDeletedAtColumn(), $now);

    $change = new Change($old, $new);

    $change->apply();
    $modelAfterSoftDeleted = Post::withTrashed()->find($new->getKey());

    \PHPUnit\Framework\assertEquals($modelAfterSoftDeleted->getAttribute($modelAfterSoftDeleted->getDeletedAtColumn()), $now);
});


it('soft delete and rollback and check if model exists', function () {
    $now = \Carbon\Carbon::now()->toDateTimeString();

    $old = createOnePost();
    $new = (clone $old);
    $new->setAttribute($new->getDeletedAtColumn(), $now);

    $change = new Change($old, $new);

    $change->apply(); // remove the model(soft delete)
    $change->rollback();
    $modelAfterRestored = Post::find($new->getKey());

    \PHPUnit\Framework\assertNull($modelAfterRestored->getAttribute($modelAfterRestored->getDeletedAtColumn()), $now);
});

it('update model with dynamic attribute assignment and check if works', function () {
    $now = \Carbon\Carbon::now()->toDateTimeString();
    $old = createOnePost(); // this not includes deleted_at column

    $new = (clone $old);
    $new->{$new->getDeletedAtColumn()} = $now; // adds deleted_at column

    $change = new Change($old, $new);

    $change->apply(); // deleted_at will added to model
    $change->rollback(); // deleted_at must be null

    $modelAfterRestored = Post::find($new->getKey());
    \PHPUnit\Framework\assertNull($modelAfterRestored->getAttribute($modelAfterRestored->getDeletedAtColumn()));
});

it('restore a model and check the change type is ok', function () {
    $now = \Carbon\Carbon::now()->toDateTimeString();
    $new = createOnePost(); // this not includes deleted_at column

    $old = (clone $new);
    $old->{$old->getDeletedAtColumn()} = $now; // adds deleted_at column

    $change = new Change($old, $new);

    \PHPUnit\Framework\assertEquals('restore', $change->getType());
});


it('restore a model and check apply will restore the model', function () {
    $now = \Carbon\Carbon::now()->toDateTimeString();
    $new = createOnePost(); // this not includes deleted_at column

    $old = (clone $new);
    $old->{$old->getDeletedAtColumn()} = $now; // adds deleted_at column

    $change = new Change($old, $new);
    $change->apply();

    $modelAfterRestored = Post::find($new->getKey());
    \PHPUnit\Framework\assertNull($modelAfterRestored->getAttribute($modelAfterRestored->getDeletedAtColumn()));
});

// test if model has get mutator
