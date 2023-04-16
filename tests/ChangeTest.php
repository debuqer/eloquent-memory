<?php
use Debuqer\EloquentMemory\ChangeTypes\ModelUpdated;
use Debuqer\EloquentMemory\ChangeTypes\ModelCreated;


/**
 * ModelUpdated Rollback is ModelUpdated
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
 * ModelCreated Rollback is ModelDeleted
 */
test('ModelCreated Rollback ', function () {
    $item = createAFakePost();
    $c = new ModelCreated(get_class($item), $item->getRawOriginal());

    expect($c->getRollbackChange()->getType())->toBe('model-deleted');
    expect($c->getRollbackChange()->getAttributes())->toBe($item->getRawOriginal());
});
