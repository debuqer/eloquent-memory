<?php


namespace Debuqer\EloquentMemory\Tests\Fixtures;

use Debuqer\EloquentMemory\CanRememberStates;

class PostWithEloquentMemory extends Post
{
    use CanRememberStates;
}
