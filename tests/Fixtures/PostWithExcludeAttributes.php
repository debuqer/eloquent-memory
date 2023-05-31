<?php


namespace Debuqer\EloquentMemory\Tests\Fixtures;

use Debuqer\EloquentMemory\CanRememberStates;
use Illuminate\Support\Arr;

class PostWithExcludeAttributes extends Post
{
    use CanRememberStates;

    public function getMemorizableAttributes()
    {
        return Arr::except($this->getRawOriginal(), ['title']);
    }
}
