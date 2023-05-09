<?php

use Debuqer\EloquentMemory\Tests\Fixtures\PostWithEloquentMemory as Post;
use Debuqer\EloquentMemory\Change;

beforeEach(function() {
    $this->post = createAPost(Post::class);
});

test('it can record model stored', function() {
    expect(\Debuqer\EloquentMemory\Change::count())->toBe(1);
    /** @var Change $change */
    $change = Change::latest('id')->first();

    expect($change->getChange()->getType())->toBe('model-created');
    expect($change->getChange()->getModelClass())->toBe(Post::class);
    expect($change->getChange()->getAttributes())->toBe($this->post->getRawOriginal());
});
