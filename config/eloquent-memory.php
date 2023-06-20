<?php

// config for Debuqer/EloquentMemory
use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;

return [
    'changes' => [
        'model-updated' => ModelUpdated::class,
        'model-created' => ModelCreated::class,
        'model-deleted' => ModelDeleted::class,
    ],

    'drivers' => [
        'default' => 'eloquent',

        'eloquent' => [
            'class_name' => \Debuqer\EloquentMemory\Repositories\Eloquent\EloquentTransitionPersistDriver::class,
            'connection' => 'default',
        ],
    ]
];
