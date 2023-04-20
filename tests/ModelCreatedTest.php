<?php
use \Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use \Debuqer\EloquentMemory\ChangeTypes\ModelDeleted;

test('ModelCreate::up will create a model with same properties', function () {
    $item = createAFakePost();
    $attributes = $item->getRawOriginal();
    $c = new ModelCreated(get_class($item), $attributes);
    $c->up();

    $item->refresh();
    expect($item->exists)->toBeTrue();

    foreach ($item->getRawOriginal() as $attr => $value) {
        expect($value)->toBe((isset($attributes[$attr]) ? $attributes[$attr] : null));
    }
});

test('ModelCreated::getRollbackChange will return an instanceof ModelDeleted with same properties ', function () {
    $item = createAFakePost();
    $c = new ModelCreated(get_class($item), $item->getRawOriginal());

    expect($c->getRollbackChange())->toBeInstanceOf(ModelDeleted::class);
    expect($c->getRollbackChange()->getOldAttributes())->toBe($item->getRawOriginal());
});

