<?php
use \Debuqer\EloquentMemory\Tests\Example\Post;
use \Debuqer\EloquentMemory\Tests\Example\User;
use \Debuqer\EloquentMemory\ChangeTypes\ModelCreated;

test('ModelCreated is applicable when: null -> model', function() {
    $old = null;
    $new = createAPost();

    expect(ModelCreated::isApplicable($old, $new))->toBe(true);
});

test('ModelCreated is applicable when: not existed model -> existed model', function() {
    $old = new Post;
    $new = createAPost();

    expect(ModelCreated::isApplicable($old, $new))->toBe(true);
});

test('ModelCreated is not applicable when: not existed model1 -> existed model2', function() {
    $old = new User;
    $new = createAPost();

    expect(ModelCreated::isApplicable($old, $new))->toBe(false);
});

test('ModelCreated is not applicable when: null -> string', function() {
    $old = null;
    $new = 'a string';

    expect(ModelCreated::isApplicable($old, $new))->toBe(false);
});

test('ModelCreated is not applicable when: null -> instance of model', function() {
    $old = null;
    $new = new Post;

    expect(ModelCreated::isApplicable($old, $new))->toBe(false);
});
