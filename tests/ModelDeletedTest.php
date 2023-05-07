<?php
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use Debuqer\EloquentMemory\ChangeTypes\ModelDeleted;

beforeEach(function () {
    $item = createAPost();
    $c = ModelDeleted::createFromModel($item);

    // change type
    $this->c = $c; // change type
    $this->item = $item; // app/Models/Post
    $this->attributes = $item->getRawOriginal(); // faker attributes
});


test('ModelDeleted::up will forceDelete a model from database', function () {
    $this->c->up();

    expect(Post::find($this->item->getKey()))->toBeNull();
});

test('ModelDeleted::getRollbackChange returns instance of ModelCreated with same properties ', function () {
    expect($this->c->getRollbackChange())->toBeInstanceOf(ModelCreated::class);
    expect($this->c->getRollbackChange()->getAttributes())->toBe($this->item->getRawOriginal());
});

test('ModelDeleted::migrate up should not care about soft delete', function() {
    $this->c->up();

    expect(Post::find($this->item->getKey()))->toBeNull();
});
