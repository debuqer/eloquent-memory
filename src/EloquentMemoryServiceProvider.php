<?php

namespace Debuqer\EloquentMemory;

use Carbon\Carbon;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EloquentMemoryServiceProvider extends PackageServiceProvider
{
    public function boot()
    {
        $this->app->bind('time', function () {
            return new Carbon();
        });

        $this->app->bind('transition-handler', function () {
            $driverName = config('eloquent-memory.driver.class_name', 'eloquent');
            $config = config('eloquent-memory.drivers.'.$driverName);

            return app()->make($config['class_name']);
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
            ->publishesServiceProvider(PublishServiceProvider::class);
    }
}
