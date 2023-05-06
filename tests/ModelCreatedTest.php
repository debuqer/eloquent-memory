<?php
use \Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use \Debuqer\EloquentMemory\ChangeTypes\ModelDeleted;

beforeEach(function () {
    $item = createAFakePost();
    $attributes = $item->getRawOriginal();
    $c = new ModelCreated(get_class($item), $attributes);

    // change type
    $this->c = $c;
    $this->item = $item;
    $this->attributes = $attributes;
});

test('ModelCreate::up will create a model with same properties', function () {
    $this->c->up();

    $this->item->refresh();
    expect($this->item->exists)->toBeTrue();

    foreach ($this->item->getRawOriginal() as $attr => $value) {
        expect($value)->toBe((isset($this->attributes[$attr]) ? $this->attributes[$attr] : null));
    }
});

test('ModelCreated::getRollbackChange will return an instanceof ModelDeleted with same properties ', function () {
    expect($this->c->getRollbackChange())->toBeInstanceOf(ModelDeleted::class);
    expect($this->c->getRollbackChange()->getOldAttributes())->toBe($this->item->getRawOriginal());
});


test('ModelCreated::persist will save a record in db', function () {
    $this->c->persist();

    expect($this->c->getModel()->exists)->toBeTrue();
});


test('ModelCreate::can be made by a db record', function() {
    $this->c->persist();

    $newC = ModelCreated::createFromPersistedRecord($this->c->getModel()); // c must be create

    expect(get_class($newC))->toBe(get_class($this->c));
    expect($newC->getParameters())->toBe($this->c->getParameters());
    expect(get_class($newC->getRollbackChange()))->toBe(get_class($this->c->getRollbackChange()));
    expect($newC->getRollbackChange()->getParameters())->toBe($this->c->getRollbackChange()->getParameters());
});

test('ModelCreate::created by db record can migrate up', function() {
    $this->c->persist();

    $newC = ModelCreated::createFromPersistedRecord($this->c->getModel()); // c must be create
    $newC->up();
    $this->item->refresh();
    expect($this->item->exists)->toBeTrue();

    foreach ($this->item->getRawOriginal() as $attr => $value) {
        expect($value)->toBe((isset($this->attributes[$attr]) ? $this->attributes[$attr] : null));
    }
});
