<?php

namespace Asadbek\OctoLaravel\Providers;

use Asadbek\OctoLaravel\Console\Commands\OctoInstall;
use Illuminate\Support\ServiceProvider;

class OctoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'octo');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
//        $this->loadViewsFrom(__DIR__.'/views', 'todolist');
//        $this->publishes([
//            __DIR__.'/views' => base_path('resources/views/wisdmlabs/todolist'),
//        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make('Asadbek\OctoLaravel\Http\Controllers\OctoBaseController');
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('octo.php'),
            ], 'octo');
            $this->commands([
                OctoInstall::class,
            ]);
        }
    }
}
