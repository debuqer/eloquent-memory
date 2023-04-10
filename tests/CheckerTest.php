<?php
use \Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemExists;
use \Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemNotExists;
use \Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsNull;
use \Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsTrash;
use \Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemsAreTheSameType;
use Debuqer\EloquentMemory\Tests\Fixtures\AlwaysTrueChecker;

/**
 * ItemExists
 */
test('ItemExists:: item exists: model given', function () {
    expect(ItemExists::define(createAPost())->evaluate())->toBeTrue();
});

test('ItemExists:: item not exists when force delete', function () {
    expect(ItemExists::define(createAPostAndForceDelete())->evaluate())->toBeFalse();
});

test('ItemExists:: item exists when soft delete', function () {
    expect(ItemExists::define(createAPostAndDelete())->evaluate())->toBeTrue();
});

test('ItemExists:: item not exists when empty model given', function () {
    expect(ItemExists::define(createEmptyPost())->evaluate())->toBeFalse();
});

test('ItemExists:: item not exists when null given', function () {
    expect(ItemExists::define(null)->evaluate())->toBeFalse();
});

test('ItemExists:: item not exists when string given', function () {
    expect(ItemExists::define('test')->evaluate())->toBeFalse();
});

test('ItemIsModel:: item is model when empty model given', function () {
    expect(ItemNotExists::define(createEmptyPost())->evaluate())->toBeTrue();
});

/**
 * ItemNotExists
 */
test('ItemIsModel:: item is not model when random object given', function () {
    expect(ItemNotExists::define(new stdClass())->evaluate())->toBeTrue();
});

/**
 * ItemIsNull
 */
test('ItemIsNull:: item is null when null given', function () {
    expect(ItemIsNull::define(null)->evaluate())->toBeTrue();
});

test('ItemIsNull:: item is null when empty string given', function () {
    expect(ItemIsNull::define('')->evaluate())->toBeTrue();
});

test('ItemIsNull:: item is null when zero string given', function () {
    expect(ItemIsNull::define(0)->evaluate())->toBeTrue();
});

test('ItemIsNull:: item is not null when model given', function () {
    expect(ItemIsNull::define(0)->evaluate())->toBeTrue();
});

test('ItemIsNull:: item is not null when random object given', function () {
    expect(ItemIsNull::define(new stdClass())->evaluate())->toBeFalse();
});

/**
 * ItemTrashed
 */
test('ItemIsTrash:: item is trashed when soft delete', function () {
    expect(ItemIsTrash::define(createAPostAndDelete())->evaluate())->toBeTrue();
});

test('ItemIsTrash:: item is not trashed when model is null', function () {
    expect(ItemIsTrash::define(null)->evaluate())->toBeFalse();
});

test('ItemIsTrash:: item is not trashed when model exists', function () {
    expect(ItemIsTrash::define(createAPost())->evaluate())->toBeFalse();
});

test('ItemIsTrash:: item is not trashed when model deleted', function () {
    expect(ItemIsTrash::define(createAPostAndForceDelete())->evaluate())->toBeFalse();
});

/**
 * @TODO ItemsAreNotTheSame
 * @TODO ItemsAreTheSame
 */

/**
 * Check not operator
 */
test('not will reverse the answer of condition', function () {
    expect(AlwaysTrueChecker::define(null)->not()->evaluate())->toBeFalse();
});

