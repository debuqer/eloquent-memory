<?php


namespace Debuqer\EloquentMemory;

use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasAttributes;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasModelClass;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasModelKey;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasOldAttributes;
use Debuqer\EloquentMemory\ChangeTypes\Concerns\HasParameters;
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
