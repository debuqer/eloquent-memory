<?php
use Debuqer\EloquentMemory\ChangeTypes\ModelSoftDeleted;

/**
 * ModelSoftDeleted
 */
test('ModelSoftDeleted::up will soft delete a model from database', function () {
    $before = createAPost();
    $after = (clone $before);
    $after->delete();

    $c = new ModelSoftDeleted(get_class($after), $after->getKey(), $before->getRawOriginal(), $after->getRawOriginal());
    $c->up();

    expect($after->refresh()->trashed())->toBeTrue();
});


/**
 * ModelSoftDeleted Rollback
 */
test('ModelSoftDeleted::getRollbackChange will return instance of ModelRestored with same properties', function () {
    $before = createAPost();
    $after = (clone $before);
    $after->delete();

    $c = new ModelSoftDeleted(get_class($after), $after->getKey(), $before->getRawOriginal(), $after->getRawOriginal());
    expect($c->getRollbackChange()->getType())->toBe('model-restored');
    expect($c->getRollbackChange()->getAttributes())->toBe( $before->getRawOriginal());
    expect($c->getRollbackChange()->getOldAttributes())->toBe( $after->getRawOriginal());
});

test('ModelSoftDeleted::down will restore a model with same properties', function () {
    // test not created
});
