<?php
use \Debuqer\EloquentMemory\Tests\Example\ExampleModel;
use \Debuqer\EloquentMemory\Patch;

it('model creation recognized properly', function () {
    $new = new ExampleModel();
    $old = null;

    $patch = new Patch($old, $new);
    \PHPUnit\Framework\assertEquals('create', $patch->getType());
});

