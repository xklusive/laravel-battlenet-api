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
     * @param string $urlSuffix API URL method
     * @param array  $options   Options
     *
     * @return ResponseInterface
     */
    protected function api($apiEndPoint, array $options)
    {
        // $options = $this->getQueryOptions($options);

        // $response = $this->client->get($this->gameParam.$apiEndPoint, $options);

        // if ($response->getStatusCode() == 200) {
        //     return $response;
        // } else {
        //     throw new \HttpResponseException('Invalid Response');
        // }

        $options = $this->getQueryOptions($options);
        $apiEndPoint = $this->gameParam.$apiEndPoint;

        return ($this->client,$apiEndPoint,$options);
    }

    /**
     * Cache the api response data if cache set to true in config file.
     *
     * @param  Illuminate\Support\Collection $response
     * @param  string $method   method name
     * @return GuzzleHttp\Psr7\Response api response
     */
    public function cache(Client $client, $apiEndPoint, $options, $method)
    {
        dd($client, $apiEndPoint, $options, $method);
        if (true === $this->hasToCache()) {
            return $this->cache->remember($this->cacheKey.snake_case($method), $this->getCacheDuration(), function () use ($response) {
                $response = $this->client->get($this->gameParam.$apiEndPoint, $options);
                return collect(json_decode($response->getBody()->getContents()));
            });
        } else {
            $response = $this->client->get($this->gameParam.$apiEndPoint, $options);
            return collect(json_decode($response->getBody()->getContents()));
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
            $result = $options['query'] + $this->getDefaultOptions();
        } else {
            $result['query'] = $options + $this->getDefaultOptions();
        }

        return $result;
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
