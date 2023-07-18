<?php

namespace Debuqer\EloquentMemory;

use Carbon\Laravel\ServiceProvider;

class PublishServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../database/migrations/create-table-model-transitions-migrations.php' => $this->app->databasePath('migrations/'.now()->format('Y_m_d_His').'create-table-model-transitions-migrations.php'),
        ]);
    }
}
