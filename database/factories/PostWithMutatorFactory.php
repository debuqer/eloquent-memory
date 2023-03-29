<?php

namespace Debuqer\EloquentMemory\Database\Factories;

use Debuqer\EloquentMemory\Tests\Example\PostWithMutator;
use Debuqer\EloquentMemory\Tests\Example\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class PostWithMutatorFactory extends PostFactory
{
    protected $model = \Debuqer\EloquentMemory\Tests\Example\PostWithMutator::class;
}

