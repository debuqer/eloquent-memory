<?php

namespace Debuqer\EloquentMemory\Tests;

use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithSoftDelete;
use Debuqer\EloquentMemory\Tests\Fixtures\User;
use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Transitions\ModelRestored;
use Debuqer\EloquentMemory\Transitions\ModelSoftDeleted;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;
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

    /**
     * @some refactor here
     */
    function getTransition(string $transitionType, $modelClass = null)
    {
        if (in_array($transitionType, [ModelCreated::class, 'model-created'])) {
            DB::beginTransaction();
            $model = $this->createAModelOf($modelClass ?? Post::class);
            $transition = [
                'model' => $model,
                'handler' => ModelCreated::createFromModel($model)
            ];
            DB::rollBack();
        }
        if (in_array($transitionType, [ModelDeleted::class, 'model-deleted'])) {
            $model = $this->createAModelOf($modelClass ?? Post::class);
            DB::beginTransaction();

            $transition = [
                'model' => $model,
                'handler' => ModelDeleted::createFromModel($model)
            ];
            DB::rollBack();
        }
        if (in_array($transitionType, [ModelRestored::class, 'model-restored'])) {
            $model = $this->createAModelOf($modelClass ?? PostWithSoftDelete::class);
            $model->delete();

            DB::beginTransaction();
            $after = (clone $model);
            $after->restore();

            $transition = [
                'model' => $model,
                'after' => $after,
                'handler' => ModelRestored::createFromModel($model, $after)
            ];
            DB::rollBack();
        }
        if (in_array($transitionType, [ModelSoftDeleted::class, 'model-soft-deleted'])) {
            $model = $this->createAModelOf($modelClass ?? PostWithSoftDelete::class);

            DB::beginTransaction();
            $after = (clone $model);
            $after->delete();

            $transition = [
                'model' => $model,
                'after' => $after,
                'handler' => ModelSoftDeleted::createFromModel($model, $after)
            ];
            DB::rollBack();

        }
        if (in_array($transitionType, [ModelUpdated::class, 'model-updated'])) {
            $model = $this->createAModelOf($modelClass ?? Post::class);

            DB::beginTransaction();
            $after = (clone $model);
            $after->update([
                'title' => 'Title changed',
                'meta' => ['new json'],
            ]);
            $after->syncOriginal();

            $transition = [
                'model' => $model,
                'after' => $after,
                'handler' => ModelUpdated::createFromModel($model, $after)
            ];
            DB::rollBack();
        }

        return $transition;
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

    function arraysAreTheSame($attrs1, $attrs2)
    {
        $allAttributes = array_merge(array_keys($attrs1, $attrs2));

        $diff = [];
        foreach ($allAttributes as $attr) {
            $valueOfArray1 = isset($attrs1[$attr]) ? $attrs1[$attr] : null;
            $valueOfArray2 = isset($attrs2[$attr]) ? $attrs2[$attr] : null;

            if ( $valueOfArray1 !== $valueOfArray2) {
                $diff[] = $attr;
            }
        }

        return count($diff) === 0;
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


}
