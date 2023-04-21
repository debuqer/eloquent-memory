<?php

namespace Debuqer\EloquentMemory;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Debuqer\EloquentMemory\Commands\EloquentMemoryCommand;

class EloquentMemoryServiceProvider extends PackageServiceProvider
{
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
            ->hasMigration('create-table-data_migrations')
            ->hasCommand(EloquentMemoryCommand::class);
    }
}
