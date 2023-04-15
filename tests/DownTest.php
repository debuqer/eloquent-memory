<?php
use \Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use \Debuqer\EloquentMemory\ChangeTypes\ModelDeleted;
use \Debuqer\EloquentMemory\ChangeTypes\ModelSoftDeleted;
use \Debuqer\EloquentMemory\ChangeTypes\ModelUpdated;
use \Debuqer\EloquentMemory\ChangeTypes\ModelRestored;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;

/**
 * ModelCreated Rollback
 */
test('ModelCreated rollback returns a ModelDeleted', function () {
    $item = createAFakePost();
    $c = new ModelCreated(get_class($item), $item->getRawOriginal());

    expect($c->getRollbackChange()->getType())->toBe(ModelDeleted::TYPE);
    expect($c->getRollbackChange()->getAttributes())->toBe($item->getRawOriginal());
    expect($c->getRollbackChange()->getModelClass())->toBe(get_class($item));
});


/**
 * ModelDeleted
 */
test('ModelDeleted rollback returns ModelCreated', function () {
    $item = createAPost();
    $c = new ModelDeleted(get_class($item), $item->getRawOriginal());

    expect($c->getRollbackChange()->getType())->toBe(ModelCreated::TYPE);
    expect($c->getRollbackChange()->getAttributes())->toBe($item->getRawOriginal());
    expect($c->getRollbackChange()->getModelClass())->toBe(get_class($item));
});

/**
 * ModelUpdated
 */
test('ModelUpdated rollback returns ModelUpdated', function () {
    $after = createAPost();
    $before = (clone $after);
    $before->update([
        'title' => 'Title changed!'
    ]);

    $c = new ModelUpdated(get_class($after), $before->getRawOriginal(), $after->getRawOriginal());

    expect($c->getRollbackChange()->getType())->toBe(ModelUpdated::TYPE);
    expect($c->getRollbackChange()->getBeforeAttributes())->toBe($after->getRawOriginal());
    expect($c->getRollbackChange()->getAfterAttributes())->toBe($before->getRawOriginal());
    expect($c->getRollbackChange()->getModelClass())->toBe(get_class($after));
});

/**
 * ModelSoftDeleted
 */
test('ModelSoftDeleted rollback will return ModelRestored', function () {
    $before = createAPost();
    $after = (clone $before);
    $after->delete();

    $c = new ModelSoftDeleted(get_class($after), $before->getRawOriginal(), $after->getRawOriginal());
    expect($c->getRollbackChange()->getType())->toBe(ModelRestored::TYPE);
    expect($c->getRollbackChange()->getBeforeAttributes())->toBe($after->getRawOriginal());
    expect($c->getRollbackChange()->getAfterAttributes())->toBe($before->getRawOriginal());
    expect($c->getRollbackChange()->getModelClass())->toBe(get_class($after));
});



/**
 * ModelRestored
 */
test('ModelRestored will return ModelSoftDelete', function () {
    $after = createAPost();
    $before = (clone $after);
    $before->delete();

    $c = new ModelRestored(get_class($after), $before->getRawOriginal(), $after->getRawOriginal());
    expect($c->getRollbackChange()->getType())->toBe(ModelSoftDeleted::TYPE);
    expect($c->getRollbackChange()->getBeforeAttributes())->toBe($after->getRawOriginal());
    expect($c->getRollbackChange()->getAfterAttributes())->toBe($before->getRawOriginal());
    expect($c->getRollbackChange()->getModelClass())->toBe(get_class($after));
});
