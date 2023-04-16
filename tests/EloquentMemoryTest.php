<?php
use \Illuminate\Support\Facades\Config;
use \Debuqer\EloquentMemory\Facades\EloquentMemory;
use \Debuqer\EloquentMemory\Exceptions\NotRecognizedChangeException;

test('change detect returns instance of change', function() {
    $change = EloquentMemory::detect(null, createAPost());

    expect($change)->toBeInstanceOf(\Debuqer\EloquentMemory\ChangeTypes\ChangeTypeInterface::class);
});

test('throws an exception if change could not be detected', function() {
    EloquentMemory::detect('unknown type', 'another unknown type');

})->throws(NotRecognizedChangeException::class);

test('only ChangeTypeInterface can be registered as change', function () {
    Config::set('eloquent-memory.eloquent-memory.changes', [new stdClass()]);
    EloquentMemory::detect('test', 'test');

})->throws(NotRecognizedChangeException::class);

test('throws error when none of changes are applicable', function () {
    EloquentMemory::detect(true, true);
})->throws(NotRecognizedChangeException::class);
