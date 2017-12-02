<?php

namespace Xklusive\BattlenetApi\Test;

use Orchestra\Testbench\TestCase as Orchestra;
use Xklusive\BattlenetApi\BattlenetApiServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('battlenet-api', [
            'domain'   => 'https://eu.api.battle.net',
            'api_key' => 'trjejxvhkw9s49etr4qpubg4h4qndjqm', // Used for testing. Do not use this in your production environment
            'locale'   => 'en_GB',
            'cache'   => false,
            'cache_duration'   => 600,
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            BattlenetApiServiceProvider::class,
        ];
    }
}
