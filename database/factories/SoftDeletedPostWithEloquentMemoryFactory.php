<?php

namespace Debuqer\EloquentMemory\Database\Factories;

use Debuqer\EloquentMemory\Tests\Fixtures\SoftDeletedPostWithEloquentMemory;


class SoftDeletedPostWithEloquentMemoryFactory extends PostFactory
{
    protected $model = SoftDeletedPostWithEloquentMemory::class;
}

