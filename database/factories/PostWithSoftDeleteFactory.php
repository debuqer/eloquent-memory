<?php

namespace Debuqer\EloquentMemory\Database\Factories;

use Debuqer\EloquentMemory\Tests\Fixtures\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class PostWithSoftDeleteFactory extends PostFactory
{
    protected $model = \Debuqer\EloquentMemory\Tests\Fixtures\PostWithSoftDelete::class;
}

