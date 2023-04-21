<?php


namespace Debuqer\EloquentMemory\Tests\Fixtures;


use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'json',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function getImageAttribute()
    {
        return 'always-fixed-image.jpg';
    }
}
