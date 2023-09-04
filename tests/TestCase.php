<?php

namespace Debuqer\EloquentMemory\Tests;

use Debuqer\EloquentMemory\EloquentMemoryServiceProvider;
use Debuqer\EloquentMemory\Repositories\TransitionPersistDriver;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Debuqer\\EloquentMemory\\Tests\\Fixtures\\Factories\\'.class_basename($modelName).'Factory'
        );

        $repository = new Fixtures\DummyTransitionDriver\EloquentTransitionPersistDriver;
        $repository->clearData();

        App::instance(TransitionPersistDriver::class, $repository);
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

        $migration = include __DIR__.'/Fixtures/migrations/create-post.php';
        $migration->up();
        $migration = include __DIR__.'/Fixtures/migrations/create-user.php';
        $migration->up();
        $migration = include __DIR__.'/Fixtures/migrations/create-table-model-transitions-migrations.php';
        $migration->up();
    }

    /**
     * @some refactor here
     */
    public function getTransition(string $transitionType, $modelClass = null)
    {
        if (in_array($transitionType, [ModelCreated::class, 'model-created'])) {
            DB::beginTransaction();
            $model = $this->createAModelOf($modelClass ?? Post::class);
            $transition = [
                'model' => $model,
                'handler' => ModelCreated::createFromModel($model),
            ];
            DB::rollBack();
        }
        if (in_array($transitionType, [ModelDeleted::class, 'model-deleted'])) {
            $model = $this->createAModelOf($modelClass ?? Post::class);
            DB::beginTransaction();

            $transition = [
                'model' => $model,
                'handler' => ModelDeleted::createFromModel($model),
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
            $after->refresh();

            $transition = [
                'model' => $model,
                'after' => $after,
                'handler' => ModelUpdated::createFromModel($after),
            ];
            DB::rollBack();
        }
        if (in_array($transitionType, ['travel-test'])) {
            $model = $this->createAModelOf($modelClass ?? Post::class);
            $transition = [
                'model' => $model,
                'handler' => ModelCreated::createFromModel($model),
            ];
        }

        return $transition;
    }

    public function getMockedDataFor($class = Post::class)
    {
        return Factory::factoryForModel($class)->raw();
    }

    public function createAModelOf($class = Post::class, $factorySource = Post::class)
    {
        $attributes = $this->getMockedDataFor($factorySource);

        return $class::create($attributes);
    }

    public function arraysAreTheSame($attrs1, $attrs2)
    {
        $allAttributes = array_merge(array_keys($attrs1, $attrs2));

        $diff = [];
        foreach ($allAttributes as $attr) {
            $valueOfArray1 = isset($attrs1[$attr]) ? $attrs1[$attr] : null;
            $valueOfArray2 = isset($attrs2[$attr]) ? $attrs2[$attr] : null;

            if ($valueOfArray1 !== $valueOfArray2) {
                $diff[] = $attr;
            }
        }

        return count($diff) === 0;
    }
}
