<?php
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\ChangeTypes\ModelUpdated;

test('ModelUpdated::up will update a model in database with given attributes', function () {
    $after = createAPost();
    $before = (clone $after);
    $before->update([
        'title' => 'Title changed!'
    ]);

    $c = new ModelUpdated(get_class($after), $after->getKey(), $before->getRawOriginal(), $after->getRawOriginal());
    $c->up();

    expect(Post::find($after->getKey())->title)->toBe($after->title);
});

test('ModelUpdated::getRollbackChange returns instanceof ModelUpdated with reversed properties', function () {
    $after = createAPost();
    $before = (clone $after);
    $before->update([
        'title' => 'Title changed!'
    ]);

    $c = new ModelUpdated(get_class($after), $after->getKey(), $before->getRawOriginal(), $after->getRawOriginal());

    expect($c->getRollbackChange()->getType())->toBe('model-updated');
    expect($c->getRollbackChange()->getOldAttributes())->toBe($after->getRawOriginal());
    expect($c->getRollbackChange()->getAttributes())->toBe($before->getRawOriginal());
    expect($c->getRollbackChange()->getModelClass())->toBe(get_class($after));
});

test('ModelUpdated::down will change properties to old state', function () {
    // test not created
});
