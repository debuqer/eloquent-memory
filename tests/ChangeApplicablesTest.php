<?php
use \Debuqer\EloquentMemory\Tests\Fixtures\Post;
use \Debuqer\EloquentMemory\Tests\Fixtures\User;
use \Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use \Debuqer\EloquentMemory\ChangeTypes\ModelUpdated;

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

test('ModelCreated is not applicable when: null -> trashedModel', function() {
    $old = null;
    $new = createAPostAndDelete();

    expect(ModelCreated::isApplicable($old, $new))->toBeFalse();
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
