<?php

namespace Debuqer\EloquentMemory\Tests\Fixtures;

use Illuminate\Database\Eloquent\SoftDeletes;

class SoftDeletedPostWithRememberState extends PostWithRememberState
{
    use SoftDeletes;
}
