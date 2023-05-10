<?php


namespace Debuqer\EloquentMemory;

use Debuqer\EloquentMemory\Transitions\Concerns\HasAttributes;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelClass;
use Debuqer\EloquentMemory\Transitions\Concerns\HasModelKey;
use Debuqer\EloquentMemory\Transitions\Concerns\HasOldAttributes;
use Debuqer\EloquentMemory\Transitions\Concerns\HasParameters;
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
