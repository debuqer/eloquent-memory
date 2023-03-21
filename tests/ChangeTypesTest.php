<?php
use \Debuqer\EloquentMemory\Tests\Example\ExampleModel;
use \Debuqer\EloquentMemory\Change;

it('create a model and check change detected as create', function () {
    $new = \Illuminate\Database\Eloquent\Factories\Factory::factoryForModel(ExampleModel::class)->make();
    $old = null;
    $change = new Change($old, $new);

    \PHPUnit\Framework\assertEquals('create', $change->getType());
});

it('create a model and check apply will create the model', function () {
    $new = \Illuminate\Database\Eloquent\Factories\Factory::factoryForModel(ExampleModel::class)->make();
    $old = null;
    $change = new Change($old, $new);

    // remove the model to check if the change can create it again or not
    $new->delete();

    $change->apply();
    $newModelAfterCreation = ExampleModel::find($new->id);

    \PHPUnit\Framework\assertNotNull($newModelAfterCreation);
    \PHPUnit\Framework\assertEquals($newModelAfterCreation->getKey(), $new->getKey());
});
