<?php

namespace Xklusive\BattlenetApi;

use Illuminate\Support\ServiceProvider;
use Xklusive\BattlenetApi\Services\WowService;

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

        // if (!class_exists('UpdateUsersTable')) {
        //     // Publish the migration
        //     $timestamp = date('Y_m_d_His', time());
        //     $this->publishes([
        //         __DIR__.'/../resources/migrations/update_users_table.php.stub' => $this->app->databasePath().'/migrations/'.$timestamp.'_update_users_table.php',
        //     ], 'migrations');
        // }

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
    }
}
