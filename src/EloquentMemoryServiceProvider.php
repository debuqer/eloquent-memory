<?php

namespace Debuqer\EloquentMemory;

use Debuqer\EloquentMemory\StorageModels\EloquentDriver;
use Debuqer\EloquentMemory\StorageModels\TransitionRepository;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EloquentMemoryServiceProvider extends PackageServiceProvider
{
    public function boot()
    {
        $this->app->bind(TransitionRepository::class, function ($app) {
            return new EloquentDriver();
        });
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('eloquent-memory')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create-table-model-transitions-migrations');
    }
}
