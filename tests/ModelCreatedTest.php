<?php
use \Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;

test('ModelCreate::up will create a model with same properties', function () {
    $item = createAFakePost();
    $attributes = $item->getRawOriginal();
    $c = new ModelCreated(get_class($item), $attributes);
    $c->up();

    $item->refresh();
    expect($item->exists)->toBeTrue();

    foreach ($item->getRawOriginal() as $attr => $value) {
        expect($value)->toBe((isset($attributes[$attr]) ? $attributes[$attr] : null));
    }
});

test('ModelCreated::down will forceDelete the model', function () {
    $item = createAFakePost();
    $c = new ModelCreated(get_class($item), $item->getRawOriginal());

    $c->up();
    $c->down();

    expect(Post::withTrashed()->find($item->getKey()))->toBeNull();
});

test('ModelCreated down throws exception when before deleting model already not exists', function () {
    $item = createAFakePost();
    $c = new ModelCreated(get_class($item), $item->getRawOriginal());

    $c->down();
})->expectException(ModelNotFoundException::class);


test('ModelCreated::getRollbackChange will return an instanceof ModelDeleted with same properties ', function () {
    $item = createAFakePost();
    $c = new ModelCreated(get_class($item), $item->getRawOriginal());

    expect($c->getRollbackChange()->getType())->toBe('model-deleted');
    expect($c->getRollbackChange()->getOldAttributes())->toBe($item->getRawOriginal());
});

