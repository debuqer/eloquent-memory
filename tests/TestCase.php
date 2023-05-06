<?php

namespace Debuqer\EloquentMemory\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
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
        $migration = include __DIR__ . '/../database/migrations/create-table-data_migrations.php';
        $migration->up();
    }
}
