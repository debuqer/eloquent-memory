<?php
use \Debuqer\EloquentMemory\Change;
use \Illuminate\Support\Facades\Config;
use \Debuqer\EloquentMemory\Exceptions\NotRecognizedChangeException;

test('change detect returns instance of change', function() {
    $change = Change::detect(null, createAPost());

    expect($change)->toBeInstanceOf(\Debuqer\EloquentMemory\ChangeTypes\ChangeTypeInterface::class);
});

test('throws an exception if change could not be detected', function() {
    Change::detect('unknown type', 'another unknown type');

})->throws(NotRecognizedChangeException::class);

test('only ChangeTypeInterface can be registered as change', function () {
    Config::set('eloquent-memory.eloquent-memory.changes', [new stdClass()]);
    Change::detect('test', 'test');

})->throws(NotRecognizedChangeException::class);

test('throws error when none of changes are applicable', function () {
    Change::detect(true, true);
})->throws(NotRecognizedChangeException::class);
