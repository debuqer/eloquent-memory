<?php
use \Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemExists;
use \Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemNotExists;
use \Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsNull;
use \Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemIsTrash;
use \Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemsAreTheSameType;
use \Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemsAreNotTheSameType;
use Debuqer\EloquentMemory\Tests\Fixtures\AlwaysTrueChecker;

/**
 * ItemExists
 */
test('ItemExists:: item exists: model given', function () {
    expect(ItemExists::setItem(createAPost())->evaluate())->toBeTrue();
});

test('ItemExists:: item not exists when force delete', function () {
    expect(ItemExists::setItem(createAPostAndForceDelete())->evaluate())->toBeFalse();
});

test('ItemExists:: item exists when soft delete', function () {
    expect(ItemExists::setItem(createAPostAndDelete())->evaluate())->toBeTrue();
});

test('ItemExists:: item not exists when empty model given', function () {
    expect(ItemExists::setItem(createEmptyPost())->evaluate())->toBeFalse();
});

test('ItemExists:: item not exists when null given', function () {
    expect(ItemExists::setItem(null)->evaluate())->toBeFalse();
});

test('ItemExists:: item not exists when string given', function () {
    expect(ItemExists::setItem('test')->evaluate())->toBeFalse();
});

test('ItemIsModel:: item is model when empty model given', function () {
    expect(ItemNotExists::setItem(createEmptyPost())->evaluate())->toBeTrue();
});

/**
 * ItemNotExists
 */
test('ItemIsModel:: item is not model when random object given', function () {
    expect(ItemNotExists::setItem(new stdClass())->evaluate())->toBeTrue();
});

/**
 * ItemIsNull
 */
test('ItemIsNull:: item is null when null given', function () {
    expect(ItemIsNull::setItem(null)->evaluate())->toBeTrue();
});

test('ItemIsNull:: item is null when empty string given', function () {
    expect(ItemIsNull::setItem('')->evaluate())->toBeTrue();
});

test('ItemIsNull:: item is null when zero string given', function () {
    expect(ItemIsNull::setItem(0)->evaluate())->toBeTrue();
});

test('ItemIsNull:: item is not null when model given', function () {
    expect(ItemIsNull::setItem(0)->evaluate())->toBeTrue();
});

test('ItemIsNull:: item is not null when random object given', function () {
    expect(ItemIsNull::setItem(new stdClass())->evaluate())->toBeFalse();
});

/**
 * ItemTrashed
 */
test('ItemIsTrash:: item is trashed when soft delete', function () {
    expect(ItemIsTrash::setItem(createAPostAndDelete())->evaluate())->toBeTrue();
});

test('ItemIsTrash:: item is not trashed when model is null', function () {
    expect(ItemIsTrash::setItem(null)->evaluate())->toBeFalse();
});

test('ItemIsTrash:: item is not trashed when model exists', function () {
    expect(ItemIsTrash::setItem(createAPost())->evaluate())->toBeFalse();
});

test('ItemIsTrash:: item is not trashed when model deleted', function () {
    expect(ItemIsTrash::setItem(createAPostAndForceDelete())->evaluate())->toBeFalse();
});


/**
 * ItemsAreTheSameType
 */
test('ItemsAreTheSameType:: item are the same type when model -> model', function () {
    expect(ItemsAreTheSameType::setItem(createAPost())->setExpect(createAPost())->evaluate())->toBeTrue();
});

test('ItemsAreTheSameType:: items are not the same type when model -> stdClass', function () {
    expect(ItemsAreTheSameType::setItem(createAPost())->setExpect(new stdClass())->evaluate())->toBeFalse();
});

test('ItemsAreTheSameType:: items are not the same type when model1 -> model2', function () {
    expect(ItemsAreTheSameType::setItem(createAPost())->setExpect(createAUser())->evaluate())->toBeFalse();
});

/**
 * Check not operator
 */
test('not will reverse the answer of condition', function () {
    expect(AlwaysTrueChecker::setItem(null)->not()->evaluate())->toBeFalse();
});

test('double not will return origin', function () {
    expect(AlwaysTrueChecker::setItem(null)->not()->not()->evaluate())->toBeTrue();
});
