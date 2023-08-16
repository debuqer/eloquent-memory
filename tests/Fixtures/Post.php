<?php

namespace Debuqer\EloquentMemory\Tests\Fixtures;

class Post extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = ['id'];

    protected $table = 'posts';

    protected $casts = [
        'meta' => 'json',
    ];

    public function getTitleAttribute($value)
    {
        return 'This title has changed';
    }
}
