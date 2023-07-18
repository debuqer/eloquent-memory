<?php

use Carbon\Carbon;
use Debuqer\EloquentMemory\Timeline;

test('Timeline: compare works with default sort', function () {
    $t = new Timeline();
    $t->insert(1, Carbon::now()->addHour());
    $t->insert(2, Carbon::now());
    $t->insert(3, Carbon::now()->subHour());

    expect($t->current())->toBe(1);
    $t->next();
    expect($t->current())->toBe(2);
    $t->next();
    expect($t->current())->toBe(3);
});

test('Timeline: compare works with latest first', function () {
    $t = new Timeline();
    $t->latestFirst();
    $t->insert(1, Carbon::now()->addHour());
    $t->insert(2, Carbon::now());
    $t->insert(3, Carbon::now()->subHour());

    expect($t->current())->toBe(1);
    $t->next();
    expect($t->current())->toBe(2);
    $t->next();
    expect($t->current())->toBe(3);
});

test('Timeline: compare works with oldest first', function () {
    $t = new Timeline();
    $t->oldestFirst();
    $t->insert(1, Carbon::now()->subHour());
    $t->insert(2, Carbon::now());
    $t->insert(3, Carbon::now()->addHour());

    expect($t->current())->toBe(1);
    $t->next();
    expect($t->current())->toBe(2);
    $t->next();
    expect($t->current())->toBe(3);
});
