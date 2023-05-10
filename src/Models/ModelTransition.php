<?php


namespace Debuqer\EloquentMemory\Models;


use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Illuminate\Database\Eloquent\Model;

class ModelTransition extends Model implements ModelTransitionInterface
{
    protected $table = 'model_transitions';

    protected $guarded = ['id'];
    protected $casts = [
        'parameters' => 'json'
    ];

    public $timestamps = true;
}
