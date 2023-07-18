<?php


namespace Debuqer\EloquentMemory\Tests\Fixtures;

use Debuqer\EloquentMemory\CanRememberStates;

class PostWithRememberState extends Post
{
    use CanRememberStates;

    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
