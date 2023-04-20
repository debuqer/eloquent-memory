<?php
use Debuqer\EloquentMemory\ChangeTypes\ModelRestored;
use Debuqer\EloquentMemory\ChangeTypes\ModelSoftDeleted;

test('ModelRestored::up will restore a model from database', function () {
    $after = createAPost();
    $before = (clone $after);
    $before->delete();

    $c = new ModelRestored(get_class($after), $after->getKey(), $before->getRawOriginal(), $after->getRawOriginal());
    $c->up();

    expect($after->refresh()->trashed())->toBeFalse();
});

test('ModelRestored::getRollbackChange will return instance of ModelSoftDeleted with same properties', function () {
    $after = createAPost();
    $before = (clone $after);
    $before->delete();

    $c = new ModelRestored(get_class($after), $after->getKey(), $before->getRawOriginal(), $after->getRawOriginal());

    expect($c->getRollbackChange())->toBeInstanceOf(ModelSoftDeleted::class);
    expect($c->getRollbackChange()->getModelKey())->toBe($c->getModelKey());
    testAttributes($c->getRollbackChange()->getOldAttributes(), $c->getAttributes());
    testAttributes($c->getRollbackChange()->getAttributes(), $c->getOldAttributes());
});
