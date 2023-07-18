<?php


namespace Debuqer\EloquentMemory\Tests\Fixtures;
use Debuqer\EloquentMemory\CanRememberStates;
use Illuminate\Database\Eloquent\Model;


class User extends Model
{
    use CanRememberStates;

    protected $guarded = ['id'];
}
