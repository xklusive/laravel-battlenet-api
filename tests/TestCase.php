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
            'api_key' => '3w7xjhkfp9w844m7aaupzrdyxh3cakww',
            'locale'   => 'fr_FR',
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
