<?php


namespace Debuqer\EloquentMemory;

use Illuminate\Database\Eloquent\Model;

class Change extends Model
{
    protected $table = 'em_changes';

    protected $guarded = ['id'];
    protected $casts = [
        'parameters' => 'json'
    ];

    public $timestamps = true;
}
