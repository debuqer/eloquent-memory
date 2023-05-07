<?php


namespace Debuqer\EloquentMemory\Tests\Fixtures;


use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends \Illuminate\Database\Eloquent\Model
{
    // @TODO should remove this
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'json',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
