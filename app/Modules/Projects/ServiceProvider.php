<?php

namespace App\Modules\Projects;

use App\Modules\Projects\Console\Commands\DispatchProjectSnapshotCommand;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                DispatchProjectSnapshotCommand::class,
            ]);
        }
    }
}
