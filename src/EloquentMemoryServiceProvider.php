<?php

namespace Debuqer\EloquentMemory;

use Carbon\Carbon;
use Debuqer\EloquentMemory\Repositories\TransitionPersistDriver;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EloquentMemoryServiceProvider extends PackageServiceProvider
{
    public function boot()
    {
        $this->app->bind('time', function () {
            return $this->getTimeManager();
        });

        $this->app->bind(TransitionPersistDriver::class, function () {
            return $this->getTransitionHandler();
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

    /**
     * @return Carbon
     */
    protected function getTimeManager()
    {
        return new Carbon();
    }

    /**
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getTransitionHandler()
    {
        $driverName = config('eloquent-memory.driver.class_name', 'eloquent');
        $config = config('eloquent-memory.drivers.'.$driverName);

        return app()->make($config['class_name']);
    }
}
