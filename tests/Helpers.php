<?php
use \Illuminate\Database\Eloquent\Factories\Factory;
use Debuqer\EloquentMemory\Tests\Example\Post;

function createAPost()
{
    return Factory::factoryForModel(Post::class)->createOne();
}
