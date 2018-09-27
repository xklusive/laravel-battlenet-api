<?php

namespace Xklusive\BattlenetApi;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\RequestException;
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
     * Battle.net Connection Options.
     *
     * @var Collection
     */
    protected $options;

    /**
     * API endpoint URL.
     *
     * @var string;
     */
    protected $apiEndPoint;

    /**
     * BattlnetHttpClient constructor.
     *
     * @param $repository Illuminate\Contracts\Cache\Repository
     */
    public function __construct(Repository $repository)
    {
        $this->cache = $repository;
        $this->client = new Client([
            'base_uri' => $this->getApiEndPoint(),
        ]);
    }

    /**
     * Creates a Mock Response based on the given array.
     * Used to imitate API response from Blizzard, without calling the actual API.
     *
     * @param $responses array
     */
    public function createMockResponse(array $responses = null)
    {
        $returnStack = collect([]);

        if ($responses) {
            foreach ($responses as $response) {
                if ($response->has('code') and $response->has('response')) {
                    $stream = Psr7\stream_for($response->get('response'));
                    $api_response = new Response(
                        $response->get('code'),
                        ['Content-Type' => 'application/json'],
                        $stream
                    );
                    $returnStack->push($api_response);
                }
            }
        }

        $mock = new MockHandler($returnStack->toArray());
        $this->setGuzzHandler(HandlerStack::create($mock));
    }

    /**
     * Create a new client with the given handler.
     * Right now only used for testing.
     *
     * @param $handler GuzzleHttp\HandlerStack
     */
    protected function setGuzzHandler(HandlerStack $handler = null)
    {
        if ($handler) {
            $this->client = new Client([
                'handler' => $handler,
                'base_uri' => $this->getApiEndPoint(),
            ]);
        }
    }

    /**
     * Make request with API url and specific URL suffix.
     *
     * @return Collection|ClientException
     */
    protected function api()
    {
        $maxAttempts = 0;
        $attempts = 0;
        $statusCode = null;
        $reasonPhrase = null;

        $serverCodes = collect([
            '504' => collect([
                'message' => 'Gateway Time-out',
                'retry' => 3,
            ]),
        ]);

        do {
            try {
                $response = $this->client->get($this->apiEndPoint, $this->options->toArray());
                $response = collect(json_decode($response->getBody()->getContents()));

                if ($attempts > 0) {
                    $response->put('attempts', $attempts);
                }

                return $response;
            } catch (ServerException $e) {
                // Catch Server errors ( return code 5xx )
                if ($e->hasResponse()) {
                    $statusCode = $e->getResponse()->getStatusCode();
                    $reasonPhrase = $e->getResponse()->getReasonPhrase();
                }

                if ($serverCodes->has($statusCode)) {
                    if ($serverCodes->get($statusCode)->get('message') == $reasonPhrase) {
                        $maxAttempts = $serverCodes->get($statusCode)->get('retry');
                        $attempts++;
                        continue;
                    }
                }
            } catch (ClientException $e) {
                // @TODO: Handle the ClientException ( HTTP 4xx codes )
                if ($e->hasResponse()) {
                    $statusCode = $e->getResponse()->getStatusCode();
                    $reasonPhrase = $e->getResponse()->getReasonPhrase();
                }
            } catch (RequestException $e) {
                // @TODO: Handle the RequestException ( when the provided domain is not valid )
                if ($e->hasResponse()) {
                    $statusCode = $e->getResponse()->getStatusCode();
                    $reasonPhrase = $e->getResponse()->getReasonPhrase();
                } else {
                    $statusCode = 909;
                    $reasonPhrase = 'Unable to resolve API domain';
                }
            }
        } while ($attempts < $maxAttempts);

        if ($statusCode and $reasonPhrase) {
            return collect([
            'error' => collect([
                'code' => $statusCode,
                'message' => $reasonPhrase,
                'attempts' => $attempts,
            ]),
        ]);
        }

        throw $e;
    }

    /**
     * Cache the api response data if cache set to true in config file.
     *
     * @param array  $options   Options
     * @param  string $method   method name
     * @param string $apiEndPoint
     * @return Collection|ClientException
     */
    public function cache($apiEndPoint, array $options, $method)
    {
        // Make sure the options we got is a collection
        $options = $this->wrapCollection($options);

        $this->options = $this->getQueryOptions($options);
        $this->apiEndPoint = $this->gameParam.$apiEndPoint;

        $this->buildCahceOptions($method);

        if ($this->options->has('cache')) {
            // The cache options are defined we need to cache the results
            return $this->cache->remember(
                $this->options->get('cache')->get('uniqKey'),
                $this->options->get('cache')->get('duration'),
                function () {
                    return $this->api();
                }
            );
        }

        return $this->api();
    }

    /**
     * Get default query options from configuration file.
     *
     * @return Collection
     */
    private function getDefaultOptions()
    {
        return collect([
            'locale' => $this->getLocale(),
            'apikey' => $this->getApiKey(),
        ]);
    }

    /**
     * Set default option if a 'query' key is provided
     * else create 'query' key with default options.
     *
     * @param Collection $options
     *
     * @return Collection api response
     */
    private function getQueryOptions(Collection $options)
    {
        // Make sure the query object is a collection.
        $query = $this->wrapCollection($options->get('query'));

        foreach ($this->getDefaultOptions() as $key => $option) {
            if ($query->has($key) === false) {
                $query->put($key, $option);
            }
        }

        $options->put('query', $query);

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

    /**
     * This method wraps the given value in a collection when applicable.
     *
     * @return Collection
     */
    public function wrapCollection($collection)
    {
        if (is_a($collection, Collection::class) === true) {
            return $collection;
        }

        return collect($collection);
    }

    /**
     * Build the cache configuration.
     *
     * @param string $method
     */
    private function buildCahceOptions($method)
    {
        if (config('battlenet-api.cache', true)) {
            if ($this->options->has('cache') === false) {
                // We don't have any cache options yet, build it from ground up.
                $cacheOptions = collect();

                $cacheOptions->put('method', snake_case($method));
                $cacheOptions->put('uniqKey', implode('.', [$this->cacheKey, $cacheOptions->get('method')]));
                $cacheOptions->put('duration', config('battlenet-api.cache_duration', 600));

                $this->options->put('cache', $cacheOptions);
            }
        }
    }
}
