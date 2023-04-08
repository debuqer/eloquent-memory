<?php
use \Debuqer\EloquentMemory\ChangeTypes\Checkers\ItemExists;

test('item exists', function () {
    $post = createAPost();

    expect(ItemExists::define($post)->condition())->toBe(true);
});

test('item not exists when force delete', function () {
    $post = createAPost();
    $post->forceDelete();

    expect(ItemExists::define($post)->condition())->toBe(false);
});

test('item not exists when sof delete', function () {
    $post = createAPost();
    $post->delete();

    expect(ItemExists::define($post)->condition())->toBe(false);
});
