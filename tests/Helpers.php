<?php
use \Illuminate\Database\Eloquent\Factories\Factory;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;

function createEmptyPost()
{
    return new Post();
}

function createAPost()
{
    return Factory::factoryForModel(Post::class)->createOne();
}

function createAPostAndDelete()
{
    $post = Factory::factoryForModel(Post::class)->createOne();
    $post->delete();

    return $post;
}

function createAPostAndForceDelete()
{
    $post = Factory::factoryForModel(Post::class)->createOne();
    $post->forceDelete();

    return $post;
}
