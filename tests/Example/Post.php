<?php


namespace Debuqer\EloquentMemory\Tests\Example;


class Post extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'json',
    ];
}
