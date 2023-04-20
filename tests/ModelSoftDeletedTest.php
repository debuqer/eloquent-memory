<?php
use Debuqer\EloquentMemory\ChangeTypes\ModelSoftDeleted;
use Debuqer\EloquentMemory\ChangeTypes\ModelRestored;

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

    expect($c->getRollbackChange())->toBeInstanceOf(ModelRestored::class);
    expect($c->getRollbackChange()->getModelKey())->toBe($c->getModelKey());
    testAttributes($c->getRollbackChange()->getOldAttributes(), $c->getAttributes());
    testAttributes($c->getRollbackChange()->getAttributes(), $c->getOldAttributes());
});

