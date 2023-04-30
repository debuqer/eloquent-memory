<?php
use \Illuminate\Database\Eloquent\Factories\Factory;
use Debuqer\EloquentMemory\Tests\Fixtures\User;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use \Illuminate\Support\Facades\DB;

function createAUser()
{
    return Factory::factoryForModel(User::class)->createOne();
}

function createEmptyPost()
{
    return new Post();
}

function createAFakePost()
{
    DB::beginTransaction();
    $model = createAPost();
    DB::rollBack();

    return $model;
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

function testAttributes($attrs1, $attrs2)
{
    foreach ($attrs1 as $attr => $value) {
        expect($value)->toBe(  $attrs2[$attr] ?? null);
    }
}
