<?php

// config for Debuqer/EloquentMemory
use Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use Debuqer\EloquentMemory\ChangeTypes\ModelDeleted;
use Debuqer\EloquentMemory\ChangeTypes\ModelRestored;
use Debuqer\EloquentMemory\ChangeTypes\ModelSoftDeleted;
use Debuqer\EloquentMemory\ChangeTypes\ModelUpdated;

return [
    'changes' => [
        ModelUpdated::class,
        ModelCreated::class,
        ModelDeleted::class,
        ModelSoftDeleted::class,
        ModelRestored::class,
    ]
];
