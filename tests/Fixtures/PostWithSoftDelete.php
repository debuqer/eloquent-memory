<?php


namespace Debuqer\EloquentMemory\Tests\Fixtures;

use Illuminate\Database\Eloquent\SoftDeletes;

class PostWithSoftDelete extends Post
{
    use SoftDeletes;
}
