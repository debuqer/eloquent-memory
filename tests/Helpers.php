<?php
use \Illuminate\Database\Eloquent\Factories\Factory;
use Debuqer\EloquentMemory\Tests\Fixtures\User;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use \Illuminate\Support\Facades\DB;

function createAUser()
{
    return Factory::factoryForModel(User::class)->createOne();
}

function createEmptyPost($class = Post::class)
{
    return new $class();
}

function createAFakePost()
{
    DB::beginTransaction();
    $model = createAPost();
    DB::rollBack();

    return $model;
}

function createAPost($class = Post::class)
{
    return Factory::factoryForModel($class)->createOne();
}

function createAPostAndDelete($class = Post::class)
{
    $post = Factory::factoryForModel($class)->createOne();
    $post->delete();

    return $post;
}

function createAPostAndForceDelete($class = Post::class)
{
    $post = Factory::factoryForModel($class)->createOne();
    $post->forceDelete();

    return $post;
}

function testAttributes($attrs1, $attrs2)
{
    foreach ($attrs1 as $attr => $value) {
        expect($value)->toBe(  $attrs2[$attr] ?? null);
    }
}
