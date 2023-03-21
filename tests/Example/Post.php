<?php


namespace Debuqer\EloquentMemory\Tests\Example;


class Post extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'json',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
