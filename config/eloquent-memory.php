<?php

// config for Debuqer/EloquentMemory
use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Transitions\ModelRestored;
use Debuqer\EloquentMemory\Transitions\ModelSoftDeleted;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;

return [
    'changes' => [
        ModelUpdated::class,
        ModelCreated::class,
        ModelDeleted::class,
        ModelSoftDeleted::class,
        ModelRestored::class,
    ]
];
