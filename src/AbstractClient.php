<?php

declare(strict_types=1);

/**
 * This file is part of Esi\LibrariesIO.
 *
 * (c) 2023-2024 Eric Sizemore <https://github.com/ericsizemore>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Esi\LibrariesIO;

use Esi\LibrariesIO\Exception\InvalidApiKeyException;
use Esi\LibrariesIO\Exception\RateLimitExceededException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\Psr6CacheStorage;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Psr\Http\Message\ResponseInterface;
use SensitiveParameter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

use function array_filter;
use function array_merge;
use function preg_match;

use const ARRAY_FILTER_USE_BOTH;

abstract class AbstractClient
{
    public const LIB_VERSION = '2.0.0';

    private const API_URL = 'https://libraries.io/api/';

    /**
     * @see https://libraries.io/account
     */
    private readonly string $apiKey;

    private readonly Client $client;

    /**
     * @param string               $apiKey        Your Libraries.io API Key
     * @param ?string              $cachePath     The path to your cache on the filesystem
     * @param array<string, mixed> $clientOptions An associative array with options to set in the initial config
     *                                            of the Guzzle client.
     *
     * @see https://docs.guzzlephp.org/en/stable/request-options.html
     *
     * @throws InvalidApiKeyException
     * @throws InvalidArgumentException
     */
    protected function __construct(#[SensitiveParameter] string $apiKey, ?string $cachePath = null, ?array $clientOptions = null)
    {
        if (preg_match('/^[0-9a-fA-F]{32}$/', $apiKey) === 0) {
            throw new InvalidApiKeyException('API key typically consists of alpha numeric characters and is 32 chars in length');
        }

        $this->apiKey = $apiKey;

        $clientOptions = self::processClientOptions($clientOptions);

        // To ease unit testing and handle cache...
        /** @var array{_mockHandler?: MockHandler} $clientOptions */
        $handlerStack = HandlerStack::create($clientOptions['_mockHandler'] ?? null);
        unset($clientOptions['_mockHandler']);

        $cachePath = Utils::validateCachePath($cachePath);

        if ($cachePath !== null) {
            $handlerStack->push(new CacheMiddleware(new PrivateCacheStrategy(
                new Psr6CacheStorage(new FilesystemAdapter('libIo', 300, $cachePath))
            )), 'cache');
        }

        $this->client = new Client(array_merge([
            'base_uri'    => self::API_URL,
            'headers'     => ['Accept' => 'application/json', ],
            'handler'     => $handlerStack,
            'http_errors' => true,
            'timeout'     => 10,
            'query'       => ['api_key' => $this->apiKey, ],
        ], $clientOptions));
    }

    /**
     * @param null|array<array-key, mixed> $options An associative array with options to set in the request.
     *
     * @see https://docs.guzzlephp.org/en/stable/request-options.html
     *
     * @throws GuzzleException
     * @throws ClientException
     * @throws RateLimitExceededException
     */
    protected function request(string $method, string $endpoint, ?array $options = null): ResponseInterface
    {
        $endpoint = Utils::normalizeEndpoint($endpoint, self::API_URL);
        $method   = Utils::normalizeMethod($method);

        $requestOptions = [
            'query' => [
                'api_key' => $this->apiKey,
            ],
        ];

        if (isset($options['query']) && \is_array($options['query'])) {
            $requestOptions['query'] += $options['query'];

            unset($options['query']);
        }

        $options        = self::processClientOptions($options);
        $requestOptions = array_merge($requestOptions, $options);

        try {
            return $this->client->request($method, $endpoint, $requestOptions);
        } catch (ClientException $clientException) {
            if ($clientException->getResponse()->getStatusCode() === 429) {
                throw new RateLimitExceededException($clientException);
            }

            throw $clientException;
        }
    }

    /**
     * @param null|array<array-key, mixed> $clientOptions
     *
     * @return array<array-key, mixed>|array{}
     */
    private static function processClientOptions(?array $clientOptions): array
    {
        $clientOptions ??= [];

        return array_filter($clientOptions, static fn ($value, $key) => match ($key) {
            'base_uri', 'handler', 'http_errors', 'query' => false, // do not override these default options
            default => true
        }, ARRAY_FILTER_USE_BOTH);
    }
}
