<?php
use Debuqer\EloquentMemory\ChangeTypes\ModelUpdated;
use Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use Debuqer\EloquentMemory\ChangeTypes\ModelDeleted;
use Debuqer\EloquentMemory\ChangeTypes\ModelSoftDeleted;


/**
 * ModelUpdated Rollback
 */
test('ModelUpdated Rollback ', function () {
    $after = createAPost();
    $before = (clone $after);
    $before->update([
        'title' => 'Title changed!'
    ]);

    $c = new ModelUpdated(get_class($after), $before->getRawOriginal(), $after->getRawOriginal());
    expect($c->getRollbackChange()->getType())->toBe('model-updated');
    expect($c->getRollbackChange()->getAfterAttributes())->toBe( $before->getRawOriginal());
    expect($c->getRollbackChange()->getBeforeAttributes())->toBe( $after->getRawOriginal());
});


/**
 * ModelCreated Rollback
 */
test('ModelCreated Rollback ', function () {
    $item = createAFakePost();
    $c = new ModelCreated(get_class($item), $item->getRawOriginal());

    expect($c->getRollbackChange()->getType())->toBe('model-deleted');
    expect($c->getRollbackChange()->getAttributes())->toBe($item->getRawOriginal());
});

/**
 * ModelDeleted Rollback
 */
test('ModelDeleted Rollback ', function () {
    $item = createAPost();
    $c = new ModelDeleted(get_class($item), $item->getRawOriginal());

    expect($c->getRollbackChange()->getType())->toBe('model-created');
    expect($c->getRollbackChange()->getAttributes())->toBe($item->getRawOriginal());
});

/**
 * ModelSoftDeleted Rollback
 */
test('ModelSoftDeleted Rollback ', function () {
    $before = createAPost();
    $after = (clone $before);
    $after->delete();

    $c = new ModelSoftDeleted(get_class($after), $before->getRawOriginal(), $after->getRawOriginal());
    expect($c->getRollbackChange()->getType())->toBe('model-restored');
    expect($c->getRollbackChange()->getAfterAttributes())->toBe( $before->getRawOriginal());
    expect($c->getRollbackChange()->getBeforeAttributes())->toBe( $after->getRawOriginal());
});


/**
 * ModelRestored Rollback
 */
test('ModelRestored Rollback ', function () {
    $before = createAPost();
    $after = (clone $before);
    $after->delete();

    $c = new ModelSoftDeleted(get_class($after), $before->getRawOriginal(), $after->getRawOriginal());
    expect($c->getRollbackChange()->getType())->toBe('model-restored');
    expect($c->getRollbackChange()->getAfterAttributes())->toBe( $before->getRawOriginal());
    expect($c->getRollbackChange()->getBeforeAttributes())->toBe( $after->getRawOriginal());
});
