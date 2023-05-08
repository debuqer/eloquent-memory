<?php
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\ChangeTypes\ModelUpdated;

beforeEach(function () {
    $before = createAPost();
    $after = (clone $before);
    $after->update([
        'title' => 'Title changed!',
        'json' => ['new json']
    ]);
    $this->before = $before;
    $this->after = $after;

    // reset to state before updating
    $after->delete();
    (new Post())->setRawAttributes($before->getRawOriginal())->save();

    $this->c = ModelUpdated::createFromModel($before, $after);
});

test('ModelUpdated::up will update a model in database with given attributes', function () {
    $this->c->up();

    expect(Post::first()->title)->toBe($this->after->title);
    expect(Post::first()->json)->toBe($this->after->json);
});

test('ModelUpdated::getRollbackChange returns instanceof ModelUpdated with reversed properties', function () {
    $after = createAPost();
    $before = (clone $after);
    $before->update([
        'title' => 'Title changed!'
    ]);

    $c = new ModelUpdated(get_class($after), $after->getKey(), $before->getRawOriginal(), $after->getRawOriginal());

    expect($c->getRollbackChange())->toBeInstanceOf(ModelUpdated::class);
    expect($c->getRollbackChange()->getModelKey())->toBe($c->getModelKey());
    testAttributes($c->getRollbackChange()->getOldAttributes(), $c->getAttributes());
    testAttributes($c->getRollbackChange()->getAttributes(), $c->getOldAttributes());
});
