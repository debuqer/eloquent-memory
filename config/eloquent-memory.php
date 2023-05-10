<?php

// config for Debuqer/EloquentMemory
use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Transitions\ModelRestored;
use Debuqer\EloquentMemory\Transitions\ModelSoftDeleted;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;

return [
    'changes' => [
        'model-updated' => ModelUpdated::class,
        'model-created' => ModelCreated::class,
        'model-deleted' => ModelDeleted::class,
        'model-soft-deleted' => ModelSoftDeleted::class,
        'model-restored' => ModelRestored::class,
    ]
];
