<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Battle.net Api configuration
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Battle.net API credentials
    |--------------------------------------------------------------------------
    |
    | Before you be able to make requests to the Battle.net API, you need to provide your API key.
    | If you don't have an API key, refer to https://dev.battle.net/docs to get an API key
    |
    */

    'api_key' => env('BATTLENET_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Battle.net Locale
    |--------------------------------------------------------------------------
    |
    | Define what locale to use for the Battle.net API response.
    | For examples: en_GB | fr_FR | de_DE | ru_RU
    |
    */
    'locale' => env('BATTLENET_LOCAL', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Battle.net api domain
    |--------------------------------------------------------------------------
    |
    | Define the region API.
    | Change [region] by the value of your choice
    | You can refer to the Battle.net API documentation https://dev.battle.net/io-docs
    | For example, if you want to request on the Europe region: 'https://eu.api.battle.net'
    |
    */
    'domain' => 'https://'.env('BATTLENET_REGION', 'eu').'.api.battle.net',

    /*
    |--------------------------------------------------------------------------
    | Battle.net api cache
    |--------------------------------------------------------------------------
    |
    | Define is the response body content is put in cache (default cache time is 10 hours),
    | using the cache driver for your application as specified by your cache configuration file.
    | Set it to false if you don't want that we manage cache.
    |
    */
    'cache' => true,

    /*
    |--------------------------------------------------------------------------
    | Battle.net api cache
    |--------------------------------------------------------------------------
    |
    | If cache is set to true, you can change here the cache time duration
    | This value is in minutes.
    |
    */
    'cache_duration' => 600,

];
