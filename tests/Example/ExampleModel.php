<?php


namespace Debuqer\EloquentMemory\Tests\Example;


class ExampleModel extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'json',
    ];
}
