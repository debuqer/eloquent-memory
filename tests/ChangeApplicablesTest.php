<?php
use \Debuqer\EloquentMemory\Tests\Fixtures\Post;
use \Debuqer\EloquentMemory\Tests\Fixtures\User;
use \Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use \Debuqer\EloquentMemory\ChangeTypes\ModelUpdated;
use \Debuqer\EloquentMemory\ChangeTypes\ModelDeleted;
use \Debuqer\EloquentMemory\ChangeTypes\ModelRestored;
use \Debuqer\EloquentMemory\ChangeTypes\ModelSoftDeleted;

test('ModelCreated is applicable when: null -> model', function() {
    $old = null;
    $new = createAPost();

    expect(ModelCreated::isApplicable($old, $new))->toBeTrue();
});

test('ModelCreated is not applicable when: notExistedModel[1] -> ExistedModel[1]', function() {
    $old = createAPostAndDelete();
    $new = createAPost();

    expect(ModelCreated::isApplicable($old, $new))->toBeFalse();
});

test('ModelCreated is not applicable when: notExistedModel[1] -> notExistedModel[2]', function() {
    $old = new User;
    $new = createAPost();

    expect(ModelCreated::isApplicable($old, $new))->toBeFalse();
});

test('ModelCreated is not applicable when: null -> string', function() {
    $old = null;
    $new = 'a string';

    expect(ModelCreated::isApplicable($old, $new))->toBeFalse();
});

test('ModelCreated is applicable when: null -> trashedModel', function() {
    $old = null;
    $new = createAPostAndDelete();

    expect(ModelCreated::isApplicable($old, $new))->toBeTrue();
});

test('ModelCreated is not applicable when: null -> emptyModel', function() {
    $old = null;
    $new = new Post;

    expect(ModelCreated::isApplicable($old, $new))->toBeFalse();
});


test('ModelUpdated is applicable when: model -> model', function() {
    $old = createAPost();
    $new = (clone $old);
    $new->update(['title' => 'New title']);

    expect(ModelUpdated::isApplicable($old, $new))->toBeTrue();
});

test('ModelUpdated is not applicable when: model -> model and no attribute changed', function() {
    $old = createAPost();
    $new = (clone $old);

    expect(ModelUpdated::isApplicable($old, $new))->toBeFalse();
});

test('ModelUpdated is not applicable when: not existed model -> existed model', function() {
    $old = new Post();
    $new = createAPost();

    expect(ModelUpdated::isApplicable($old, $new))->toBeFalse();
});

test('ModelUpdated is applicable when: original value changes but mutated value shows fixed value', function () {
    $old = createAPost();
    $new = (clone $old);
    $new->update(['image' => 'new-image.jpg']); // there is a mutation function in models/Post

    expect(ModelUpdated::isApplicable($old, $new))->toBeTrue();
});

test('ModelUpdated is applicable when original value changes but mutated value shows fixed value', function () {
    $old = createAPost();
    $new = (clone $old);
    $new->update(['image' => 'new-image.jpg']); // there is a mutation function in models/Post

    expect(ModelUpdated::isApplicable($old, $new))->toBeTrue();
});

test('ModelUpdated is not applicable when two different model presents', function () {
    $old = createAPost();
    $new = createAPost();

    expect(ModelUpdated::isApplicable($old, $new))->toBeFalse();
});

test('ModelDeleted is applicable when model -> null', function () {
    $old = createAPost();
    $new = null;

    expect(ModelDeleted::isApplicable($old, $new))->toBeTrue();
});

test('ModelDeleted is applicable when model -> deletedModel', function () {
    $old = createAPost();
    $new = (clone $old);
    $new->forceDelete();

    expect(ModelDeleted::isApplicable($old, $new))->toBeTrue();
});

test('ModelDeleted is not applicable when deletedModel -> deletedModel', function () {
    $old = createAPost();
    $new = (clone $old);
    $old->forceDelete();
    $new->forceDelete();

    expect(ModelDeleted::isApplicable($old, $new))->toBeFalse();
});

test('ModelDeleted is not applicable when null -> deletedModel', function () {
    $old = null;
    $new = createAPost();
    $new->forceDelete();

    expect(ModelDeleted::isApplicable($old, $new))->toBeFalse();
});

test('ModelDeleted is applicable when softDeletedModel -> deletedModel', function () {
    $old = createAPost();
    $new = (clone $old);
    $old->delete();
    $new->forceDelete();

    expect(ModelDeleted::isApplicable($old, $new))->toBeTrue();
});

test('ModelDeleted is applicable when using model without soft delete', function () {
    $old = createAUser();
    $new = (clone $old);
    $new->delete();

    expect(ModelDeleted::isApplicable($old, $new))->toBeTrue();
});

test('ModelDeleted is not applicable when model -> softDeletedModel', function () {
    $old = createAPost();
    $new = (clone $old);
    $new->delete();

    expect(ModelDeleted::isApplicable($old, $new))->toBeFalse();
});

test('ModelDeleted is not applicable when trashedModel -> not exists model', function () {
    $old = createAPostAndDelete();
    $new = (clone $old);
    $new->delete();

    expect(ModelDeleted::isApplicable($old, $new))->toBeFalse();
});

test('ModelRestored is applicable when trashedModel -> existed model', function () {
    $old = createAPost();
    $new = (clone $old);
    $old->delete();

    expect(ModelRestored::isApplicable($old, $new))->toBeTrue();
});


test('ModelRestored is not applicable when null -> existed model', function () {
    $old = null;
    $new = createAPost();

    expect(ModelRestored::isApplicable($old, $new))->toBeFalse();
});

test('ModelRestored is not applicable when trashedModel -> trashedModel', function () {
    $old = createAPost();
    $old->delete();
    $new = (clone $old);

    expect(ModelRestored::isApplicable($old, $new))->toBeFalse();
});

test('ModelRestored is not applicable when two different type of model given', function () {
    $old = createAPost();
    $new = createAUser();
    $old->delete();

    expect(ModelRestored::isApplicable($old, $new))->toBeFalse();
});


test('ModelRestored is not applicable when two different model given', function () {
    $old = createAPost();
    $new = createAPost();
    $old->delete();

    expect(ModelRestored::isApplicable($old, $new))->toBeFalse();
});

test('ModelSoftDeleted is applicable when model -> trashedModel', function () {
    $old = createAPost();
    $new = (clone $old);
    $new->delete();

    expect(ModelSoftDeleted::isApplicable($old, $new))->toBeTrue();
});

test('ModelSoftDeleted is not applicable when trashedModel -> trashedModel', function () {
    $old = createAPostAndDelete();
    $new = (clone $old);

    expect(ModelSoftDeleted::isApplicable($old, $new))->toBeFalse();
});

test('ModelSoftDeleted is not applicable when model -> null', function () {
    $old = createAPost();
    $new = null;

    expect(ModelSoftDeleted::isApplicable($old, $new))->toBeFalse();
});

test('ModelSoftDeleted is not applicable when two different model given', function () {
    $old = createAPost();
    $new = createAPost();
    $new->delete();

    expect(ModelSoftDeleted::isApplicable($old, $new))->toBeFalse();
});

test('ModelSoftDeleted is not applicable when two different type of model given', function () {
    $old = createAPost();
    $new = createAUser();
    $new->delete();

    expect(ModelSoftDeleted::isApplicable($old, $new))->toBeFalse();
});
