<?php

namespace Xklusive\BattlenetApi;

use Illuminate\Support\ServiceProvider;
use Xklusive\BattlenetApi\Services\WowService;
use Xklusive\BattlenetApi\Services\DiabloService;

class BattlenetApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../resources/config/battlenet-api.php' => config_path('battlenet-api.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../resources/config/battlenet-api.php',
            'battlenet-api'
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('WowService', WowService::class);
        $this->app->bind('DiabloService', DiabloService::class);
    }
}
