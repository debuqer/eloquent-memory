<?php


namespace Debuqer\EloquentMemory\Tests\Fixtures;

use Illuminate\Database\Eloquent\SoftDeletes;

class SoftDeletedPostWithEloquentMemory extends PostWithEloquentMemory {
    use SoftDeletes;
}
