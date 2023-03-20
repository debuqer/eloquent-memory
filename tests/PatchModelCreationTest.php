<?php
use \Debuqer\EloquentMemory\Tests\Example\ExampleModel;
use \Debuqer\EloquentMemory\Change;

it('model creation recognized properly', function () {
    $new = new ExampleModel();
    $old = null;

    $change = new Change($old, $new);
    \PHPUnit\Framework\assertEquals('create', $change->getType());
});

it('model creation patch apply will result creating model', function () {
    $new = new ExampleModel();
    $old = null;

    $change = new Change($old, $new);
    $change->apply();
});
