<?php
use \Debuqer\EloquentMemory\Change;

test('change detect returns instance of change', function() {
    $change = Change::detect(null, createAPost());

    expect($change)->toBeInstanceOf(\Debuqer\EloquentMemory\ChangeTypes\ChangeTypeInterface::class);
});

test('throws an exception if change could not be detected', function() {

    Change::detect('unknown type', 'another unknown type');
})->throws(\Debuqer\EloquentMemory\Exceptions\NotRecognizedChangeException::class);

//test('only ChangeTypeInterface can be registered as change', function () {
//    // @TODO
//});
