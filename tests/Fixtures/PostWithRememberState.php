<?php


namespace Debuqer\EloquentMemory\Tests\Fixtures;

use Debuqer\EloquentMemory\CanRememberStates;

class PostWithRememberState extends Post
{
    use CanRememberStates;
}
