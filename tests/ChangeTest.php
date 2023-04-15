<?php
use Debuqer\EloquentMemory\ChangeTypes\ModelUpdated;

/**
 * ModelCreated Rollback
 */
test('11', function () {
    $after = createAPost();
    $before = (clone $after);
    $before->update([
        'title' => 'Title changed!'
    ]);

    $c = new ModelUpdated(get_class($after), $before->getRawOriginal(), $after->getRawOriginal());

    dd($c);
});
