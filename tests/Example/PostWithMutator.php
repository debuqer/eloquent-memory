<?php


namespace Debuqer\EloquentMemory\Tests\Example;


use Illuminate\Database\Eloquent\SoftDeletes;

class PostWithMutator extends Post {
    use SoftDeletes;

    public function setTitleAttribute()
    {
        return $this->title . 'mutator';
    }

    public function getTitleAttribute()
    {
        return $this->title . 'mutated';
    }
}
