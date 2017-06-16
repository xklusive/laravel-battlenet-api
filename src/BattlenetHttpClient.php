<?php

namespace Xklusive\BattlenetApi;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

use Psr\Http\Message\ResponseInterface;
use Illuminate\Contracts\Cache\Repository;

/**
 * @author Guillaume Meheust <xklusive91@gmail.com>
 */
class BattlenetHttpClient
{
    /**
     * @var Repository
     */
    protected $cache;

    /**
     * @var string
     */
    protected $cacheKey = 'xklusive.battlenetapi.cache';

    /**
     * Http client.
     *
     * @var object GuzzleHttp\Client
     */
    protected $client;

    /**
     * Game name for url prefix.
     *
     * @var string
     */
    protected $gameParam;

    /**
     * BattlnetHttpClient constructor.
     */
    public function __construct(Repository $repository)
    {
        $this->cache = $repository;
        $this->client = new Client([
            'base_uri' => $this->getApiEndPoint(),
        ]);
    }

    /**
     * Make request with API url and specific URL suffix.
     *
     * @return Collection / GuzzleHttp\Exception\ClientException
     */
    protected function api()
    {
        $maxAttempts = 1;
        $attempts = 0;

        $statusCodes = [
            // '401' => [
            //     'message' => 'Unauthorized',
            //     'retry' => 1,
            // ],
            // '403' => [
            //     'message' => 'Forbidden',
            //     'retry' => 1,
            // ],
            '504' => [
                'message' => 'Gateway Timeout',
                'retry' => 5,
            ]
        ];

        do {
            try {
                $response = $this->client->get($this->apiEndPoint, $this->options);

                return collect(json_decode($response->getBody()->getContents()));
            } catch (\Exception $e) {
                $statusCode = $e->getResponse()->getStatusCode();
                $reasonPhrase = $e->getResponse()->getReasonPhrase();

                Debugbar::warning("We got an error -- $statusCode -- $reasonPhrase");
                if ( array_key_exists($statusCode, $statusCodes) ) {
                    if ( $statusCodes[$statusCode]['message'] == $reasonPhrase) {
                        Debugbar::warning("Error is retriable, continue with attempt #$attempts out of $maxAttempts");
                        $maxAttempts = $statusCodes[$statusCode]['retry'];
                        $attempts++;
                        continue;
                    }
                } else {
                    $maxAttempts = $attempts;
                    Debugbar::warning("Error is non retriable exiting...");
                }
            }    
        } while ($attempts < $maxAttempts);

        Debugbar::warning("We have no retries left. Lets return to the fallback function.");
        return $e;
    }

    /**
     * Cache the api response data if cache set to true in config file.
     *
     * @param string $urlSuffix API URL method
     * @param array  $options   Options
     * @param  string $method   method name
     * @return Collection / GuzzleHttp\Exception\ClientException
     */
    public function cache($apiEndPoint, array $options, $method)
    {
        $this->options = $this->getQueryOptions($options);
        $this->apiEndPoint = $this->gameParam.$apiEndPoint;

        $this->options['cache']['method'] = snake_case($method);
        $uniqCacheKey = implode('.',[$this->cacheKey,implode('.',$this->options['cache'])]);

        if (true === $this->hasToCache()) {
            return $this->cache->remember(
                $uniqCacheKey, 
                $this->getCacheDuration(), 
                function () { 
                    return $this->api();
                    // return collect(json_decode($response->getBody()->getContents()));
                }
            );
        } else {
            return $this->api();
        }
    }

    /**
     * Get default query options from configuration file.
     *
     * @return array
     */
    private function getDefaultOptions()
    {
        return [
            'locale' => $this->getLocale(),
            'apikey' => $this->getApiKey(),
        ];
    }

    /**
     * Set default option if a 'query' key is provided
     * else create 'query' key with default options.
     *
     * @param array $options
     *
     * @return Illuminate\Support\Collection api response
     */
    private function getQueryOptions(array $options = [])
    {
        if (isset($options['query'])) {
            $options['query'] = $options['query'] + $this->getDefaultOptions();
        } else {
            $options['query'] += $this->getDefaultOptions();
        }

        return $options;
    }

    /**
     * Get API domain provided in configuration.
     *
     * @return string
     */
    private function getApiEndPoint()
    {
        return config('battlenet-api.domain');
    }

    /**
     * Get API key provided in configuration.
     *
     * @return string
     */
    private function getApiKey()
    {
        return config('battlenet-api.api_key');
    }

    /**
     * Get API locale provided in configuration.
     *
     * @return string
     */
    private function getLocale()
    {
        return config('battlenet-api.locale', 'eu');
    }

    private function hasToCache()
    {
        return config('battlenet-api.cache', true);
    }

    private function getCacheDuration()
    {
        return config('battlenet-api.cache_duration', 600);
    }
}
