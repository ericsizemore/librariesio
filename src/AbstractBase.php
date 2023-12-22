<?php

declare(strict_types=1);

/**
 * LibrariesIO - A simple API wrapper/client for the Libraries.io API.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @package   LibrariesIO
 * @link      https://www.secondversion.com/
 * @version   1.0.0
 * @copyright (C) 2023 Eric Sizemore
 * @license   The MIT License (MIT)
 */
namespace Esi\LibrariesIO;

// Exceptions and Attributes
use GuzzleHttp\Exception\GuzzleException;
use SensitiveParameter;
use InvalidArgumentException;
use JsonException;

// Guzzle
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

// Cache
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Kevinrob\GuzzleCache\Storage\Psr6CacheStorage;

// Functions and constants
use function is_dir, is_writable, json_decode, preg_match;
use const JSON_THROW_ON_ERROR;

/**
 * LibrariesIO - A simple API wrapper/client for the Libraries.io API.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @package   LibrariesIO
 * @link      https://www.secondversion.com/
 * @version   1.0.0
 * @copyright (C) 2023 Eric Sizemore
 * @license   The MIT License (MIT)
 *
 * Copyright (C) 2023 Eric Sizemore. All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
abstract class AbstractBase
{
    /**
     * GuzzleHttp Client
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Base API endpoint.
     *
     * @var string
     */
    protected const API_URL = 'https://libraries.io/api/';

    /**
     * Libraries.io API key.
     *
     * @see https://libraries.io/account
     * @var ?string
     */
    protected ?string $apiKey = null;

    /**
     * Path to your cache folder on the file system.
     *
     * @var ?string
     */
    protected ?string $cachePath = null;

    /**
     * Constructor.
     *
     * @param string  $apiKey    Your Libraries.io API Key
     * @param ?string $cachePath The path to your cache on the filesystem
     */
    public function __construct(
        #[SensitiveParameter]
        string $apiKey,
        ?string $cachePath = null
    )
    {
        if (preg_match('/^[0-9a-fA-F]{32}$/', $apiKey) === 0) {
            throw new InvalidArgumentException('API key appears to be invalid, keys are typically alpha numeric and 32 chars in length');
        }

        $this->apiKey = $apiKey;

        if ($cachePath !== null && is_dir($cachePath) && is_writable($cachePath)) {
            $this->cachePath = $cachePath;
        }
    }

    /**
     * Builds our GuzzleHttp client.
     *
     * @access protected
     * @param  array<string, int|string> $query
     * @return Client
     */
    protected function makeClient(?array $query = null): Client
    {
        // Some endpoints do not require any query parameters
        if ($query === null) {
            $query = [];
        }

        // Add the API key to the query
        $query['api_key'] = $this->apiKey; 

        // Client options
        $options = [
            'base_uri' => self::API_URL,
            'query'    => $query,
            'headers' => [
                'Accept' => 'application/json'
            ],
            'http_errors' => true
        ];

        // If we have a cache path, create our Cache handler
        if ($this->cachePath !== null) {
            // Create default HandlerStack
            $stack = HandlerStack::create();

            // Add this middleware to the top with `push`
            $stack->push(new CacheMiddleware(
                new PrivateCacheStrategy(
                    new Psr6CacheStorage(
                        new FilesystemAdapter('libio', 60, $this->cachePath)
                    )
                )
            ), 'cache');

            // Add handler to $options
            $options += ['handler' => $stack];
        }

        // Build client
        $this->client = new Client($options);

        return $this->client;
    }

    /**
     * Performs the actual request to the API.
     *
     * @param array<string, int|string> $options
     * @return ResponseInterface
     * @throws ClientException|GuzzleException
     */
    abstract function makeRequest(array $options): ResponseInterface;

    /**
     * Decodes the jSON returned from the API. Returns as an associative array.
     *
     * @param ResponseInterface $response The response object from makeRequest()
     * @return array<mixed>
     * @throws JsonException
     */
    public function toArray(ResponseInterface $response): array
    {
        /** @var array<mixed> $json **/
        $json = json_decode($response->getBody()->getContents(), true, flags: JSON_THROW_ON_ERROR);

        return $json;
    }

    /**
     * Decodes the jSON returned from the API. Returns as an array of objects.
     *
     * @param ResponseInterface $response The response object from makeRequest()
     * @return array<mixed>
     * @throws JsonException
     */
    public function toObject(ResponseInterface $response): array
    {
        /** @var array<mixed> $json **/
        $json = json_decode($response->getBody()->getContents(), false, flags: JSON_THROW_ON_ERROR);
        
        return $json;
    }
}
