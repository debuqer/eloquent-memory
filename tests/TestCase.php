<?php

namespace Debuqer\EloquentMemory\Tests;

use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\Tests\Fixtures\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase as Orchestra;
use Debuqer\EloquentMemory\EloquentMemoryServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Debuqer\\EloquentMemory\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            EloquentMemoryServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__ . '/../database/migrations/create-post.php';
        $migration->up();
        $migration = include __DIR__ . '/../database/migrations/create-user.php';
        $migration->up();
        $migration = include __DIR__ . '/../database/migrations/create-table-model-transitions-migrations.php';
        $migration->up();
    }

    function getMockedDataFor($class = Post::class)
    {
        return Factory::factoryForModel($class)->raw();
    }

    function createAModelOf($class = Post::class, $factorySource = Post::class)
    {
        $attributes = $this->getMockedDataFor($factorySource);

        return $class::create($attributes);
    }

    function getFilledModelOf($class = Post::class, $factorySource = Post::class)
    {
         DB::beginTransaction();
         $item = $this->createAModelOf($class, $factorySource);
         DB::rollBack();

         return $item;
    }

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
        $model = $this->createAPost();
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

    function expectAttributesAreTheSame($attrs1, $attrs2)
    {
        foreach ($attrs1 as $attr => $value) {
            expect($value)->toBe(  $attrs2[$attr] ?? null);
        }
    }
}
