<?php

declare(strict_types=1);

/**
 * LibrariesIO - A simple API wrapper/client for the Libraries.io API.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 *
 * @version   1.1.0
 *
 * @copyright (C) 2023-2024 Eric Sizemore
 * @license   The MIT License (MIT)
 *
 * Copyright (C) 2023-2024 Eric Sizemore <https://www.secondversion.com/>.
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

namespace Esi\LibrariesIO;

use Esi\LibrariesIO\Exception\RateLimitExceededException;
use GuzzleHttp\Exception\{
    ClientException,
    GuzzleException
};
use GuzzleHttp\{
    Client,
    HandlerStack
};
use InvalidArgumentException;
use JsonException;
use Kevinrob\GuzzleCache\{
    CacheMiddleware,
    Storage\Psr6CacheStorage,
    Strategy\PrivateCacheStrategy
};
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use SensitiveParameter;
use stdClass;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

use function implode;
use function in_array;
use function is_dir;
use function is_writable;
use function json_decode;
use function preg_match;
use function str_contains;

use const JSON_THROW_ON_ERROR;

/**
 * Main class.
 *
 * @see \Esi\LibrariesIO\Tests\LibrariesIOTest
 */
class LibrariesIO
{
    /**
     * GuzzleHttp Client.
     */
    public ?Client $client = null;

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
     */
    private ?string $apiKey = null;

    /**
     * Path to your cache folder on the file system.
     */
    private ?string $cachePath = null;

    /**
     * Constructor.
     *
     * @param string  $apiKey    Your Libraries.io API Key
     * @param ?string $cachePath The path to your cache on the filesystem
     */
    public function __construct(#[SensitiveParameter] string $apiKey, ?string $cachePath = null)
    {
        if (preg_match('/^[0-9a-fA-F]{32}$/', $apiKey) === 0) {
            throw new InvalidArgumentException('API key appears to be invalid, keys are typically alpha numeric and 32 chars in length');
        }

        $this->apiKey = $apiKey;

        if (is_dir((string) $cachePath) && is_writable((string) $cachePath)) {
            $this->cachePath = $cachePath;
        }
    }

    /**
     * Builds our GuzzleHttp client.
     *
     * @param array<string, int|string> $query
     */
    private function makeClient(?array $query = null): Client
    {
        // From the test suite/PHPUnit?
        if ($this->client instanceof Client && $this->apiKey === '098f6bcd4621d373cade4e832627b4f6') {
            return $this->client;
        }

        //@codeCoverageIgnoreStart
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
            'headers'  => [
                'Accept' => 'application/json',
            ],
            'http_errors' => true,
        ];

        // If we have a cache path, create our Cache handler
        if ($this->cachePath !== null) {
            // Create default HandlerStack
            $stack = HandlerStack::create();

            // Add this middleware to the top with `push`
            $stack->push(new CacheMiddleware(new PrivateCacheStrategy(
                new Psr6CacheStorage(new FilesystemAdapter('', 300, $this->cachePath))
            )), 'cache');

            // Add handler to $options
            $options += ['handler' => $stack];
        }

        // Build client
        $this->client = new Client($options);

        return $this->client;
        //@codeCoverageIgnoreEnd
    }

    /**
     * Performs the actual client request.
     *
     * @throws ClientException|GuzzleException|RateLimitExceededException|RuntimeException
     */
    private function makeRequest(string $endpoint, string $method = 'GET'): ResponseInterface
    {
        // Attempt the request
        try {
            $method = strtoupper($method);

            $request = match($method) {
                'GET', 'POST', 'PUT', 'DELETE' => $this->client?->request($method, $endpoint),
                default => $this->client?->request('GET', $endpoint)
            };

            // Shouldn't happen...
            if (!$request instanceof ResponseInterface) {
                //@codeCoverageIgnoreStart
                throw new RuntimeException('$this->client does not appear to be a valid \GuzzleHttp\Client instance');
                //@codeCoverageIgnoreEnd
            }

            return $request;
        } catch (ClientException $clientException) {
            if ($clientException->getResponse()->getStatusCode() === 429) {
                throw new RateLimitExceededException('Libraries.io API rate limit exceeded.', previous: $clientException);
            }

            throw $clientException;
        }
    }

    /**
     * Performs a request to the 'platforms' endpoint.
     *
     * @throws InvalidArgumentException|ClientException|GuzzleException|RateLimitExceededException
     */
    public function platform(string $endpoint = 'platforms'): ResponseInterface
    {
        // The only valid endpoint is 'platforms' currently
        if ($endpoint !== 'platforms') {
            throw new InvalidArgumentException('Invalid endpoint specified. Must be one of: platforms');
        }

        // Build query
        $this->makeClient();

        return $this->makeRequest($endpoint);
    }

    /**
     * Performs a request to the 'project' endpoint and a subset endpoint, which can be:
     * contributors, dependencies, dependent_repositories, dependents, search, sourcerank, or project
     *
     * @param array<string, int|string> $options
     *
     * @throws InvalidArgumentException|ClientException|GuzzleException|RateLimitExceededException
     */
    public function project(string $endpoint, array $options): ResponseInterface
    {
        // Make sure we have the format and options for $endpoint
        $endpointParameters = self::endpointParameters('project', $endpoint);

        /** @var array<int, string> $endpointOptions * */
        $endpointOptions = $endpointParameters['options'];

        self::verifyEndpointOptions($endpointOptions, $options);

        // Build query
        $query = [
            'page'     => $options['page'] ?? 1,
            'per_page' => $options['per_page'] ?? 30,
        ];

        // If on the 'search' endpoint, we have to provide the query and sort parameters.
        if ($endpoint === 'search') {
            $query += [
                'q'    => $options['query'],
                'sort' => self::searchVerifySortOption(/** @phpstan-ignore-line **/$options['sort']),
            ];

            // Search can also have: 'languages', 'licenses', 'keywords', 'platforms' as additional parameters
            $additionalParams = self::searchAdditionalParams($options);

            if ($additionalParams !== []) {
                $query += $additionalParams;
            }
        }

        // Build the client
        $this->makeClient($query);

        // Attempt the request
        $endpointParameters['format'] = self::processEndpointFormat(/** @phpstan-ignore-line **/$endpointParameters['format'], $options);

        return $this->makeRequest($endpointParameters['format']);
    }

    /**
     * Performs a request to the 'repository' endpoint and a subset endpoint, which can be:
     * dependencies, projects, or repository
     *
     * @param array<string, int|string> $options
     *
     * @throws InvalidArgumentException|ClientException|GuzzleException|RateLimitExceededException
     */
    public function repository(string $endpoint, array $options): ResponseInterface
    {
        // Make sure we have the format and options for $endpoint
        $endpointParameters = self::endpointParameters('repository', $endpoint);

        /** @var array<int, string> $endpointOptions * */
        $endpointOptions = $endpointParameters['options'];

        self::verifyEndpointOptions($endpointOptions, $options);

        // Build query
        $this->makeClient([
            // Using pagination?
            'page'     => $options['page'] ?? 1,
            'per_page' => $options['per_page'] ?? 30,
        ]);

        // Attempt the request
        $endpointParameters['format'] = self::processEndpointFormat(/** @phpstan-ignore-line **/$endpointParameters['format'], $options);

        return $this->makeRequest($endpointParameters['format']);
    }

    /**
     * Performs a request to the 'user' endpoint and a subset endpoint, which can be:
     * dependencies, package_contributions, packages, repositories, repository_contributions, or subscriptions
     *
     * @param array<string, int|string> $options
     *
     * @throws InvalidArgumentException|ClientException|GuzzleException|RateLimitExceededException
     */
    public function user(string $endpoint, array $options): ResponseInterface
    {
        // Make sure we have the format and options for $endpoint
        $endpointParameters = self::endpointParameters('user', $endpoint);

        /** @var array<int, string> $endpointOptions * */
        $endpointOptions = $endpointParameters['options'];

        self::verifyEndpointOptions($endpointOptions, $options);

        // Build query
        $this->makeClient([
            'page'     => $options['page'] ?? 1,
            'per_page' => $options['per_page'] ?? 30,
        ]);

        // Attempt the request
        $endpointParameters['format'] = self::processEndpointFormat(/** @phpstan-ignore-line **/$endpointParameters['format'], $options);

        return $this->makeRequest($endpointParameters['format']);
    }

    /**
     * Performs a request to the 'subscription' endpoint and a subset endpoint, which can be:
     * subscribe, check, update, unsubscribe
     *
     * @param array<string, int|string> $options
     *
     * @throws InvalidArgumentException|ClientException|GuzzleException|RateLimitExceededException
     */
    public function subscription(string $endpoint, array $options): ResponseInterface
    {
        // Make sure we have the format and options for $endpoint
        $endpointParameters = self::endpointParameters('subscription', $endpoint);

        /** @var array<int, string> $endpointOptions * */
        $endpointOptions = $endpointParameters['options'];

        self::verifyEndpointOptions($endpointOptions, $options);

        // Build query
        if (isset($options['include_prerelease'])) {
            $query = ['include_prerelease' => $options['include_prerelease']];
        }

        $this->makeClient($query ?? []);

        // Attempt the request
        $endpointParameters['format'] = self::processEndpointFormat(/** @phpstan-ignore-line **/$endpointParameters['format'], $options);

        return $this->makeRequest($endpointParameters['format'], /** @phpstan-ignore-line **/$endpointParameters['method']);
    }

    /**
     * Processes the available parameters for a given endpoint.
     *
     * @return array<string, array<string>|string>
     *
     * @throws InvalidArgumentException
     */
    private static function endpointParameters(string $endpoint, string $subset): array
    {
        static $projectParameters = [
            'contributors'           => ['format' => ':platform/:name/contributors', 'options' => ['platform', 'name']],
            'dependencies'           => ['format' => ':platform/:name/:version/dependencies', 'options' => ['platform', 'name', 'version']],
            'dependent_repositories' => ['format' => ':platform/:name/dependent_repositories', 'options' => ['platform', 'name']],
            'dependents'             => ['format' => ':platform/:name/dependents', 'options' => ['platform', 'name']],
            'search'                 => ['format' => 'search', 'options' => ['query', 'sort']],
            'sourcerank'             => ['format' => ':platform/:name/sourcerank', 'options' => ['platform', 'name']],
            'project'                => ['format' => ':platform/:name', 'options' => ['platform', 'name']],
        ];

        static $repositoryParameters = [
            'dependencies' => ['format' => 'github/:owner/:name/dependencies', 'options' => ['owner', 'name']],
            'projects'     => ['format' => 'github/:owner/:name/projects', 'options' => ['owner', 'name']],
            'repository'   => ['format' => 'github/:owner/:name', 'options' => ['owner', 'name']],
        ];

        static $userParameters = [
            'dependencies'             => ['format' => 'github/:login/dependencies', 'options' => ['login']],
            'package_contributions'    => ['format' => 'github/:login/project-contributions', 'options' => ['login']],
            'packages'                 => ['format' => 'github/:login/projects', 'options' => ['login']],
            'repositories'             => ['format' => 'github/:login/repositories', 'options' => ['login']],
            'repository_contributions' => ['format' => 'github/:login/repository-contributions', 'options' => ['login']],
            'subscriptions'            => ['format' => 'subscriptions', 'options' => []],
            'user'                     => ['format' => 'github/:login', 'options' => ['login']],
        ];

        static $subscriptionParameters = [
            'subscribe'   => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name', 'include_prerelease'], 'method' => 'post'],
            'check'       => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name'], 'method' => 'get'],
            'update'      => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name', 'include_prerelease'], 'method' => 'put'],
            'unsubscribe' => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name'], 'method' => 'delete'],
        ];

        return match($endpoint) {
            'project'      => $projectParameters[$subset] ?? throw new InvalidArgumentException('Invalid endpoint subset specified.'),
            'repository'   => $repositoryParameters[$subset] ?? throw new InvalidArgumentException('Invalid endpoint subset specified.'),
            'user'         => $userParameters[$subset] ?? throw new InvalidArgumentException('Invalid endpoint subset specified.'),
            'subscription' => $subscriptionParameters[$subset] ?? throw new InvalidArgumentException('Invalid endpoint subset specified.'),
            default        => throw new InvalidArgumentException('Invalid endpoint subset specified.')
        };
    }

    /**
     * Each endpoint class will have a 'subset' of endpoints that fall under it. This
     * function handles returning a formatted endpoint for the Client.
     *
     * @param array<string, int|string> $options
     */
    private static function processEndpointFormat(string $format, array $options): string
    {
        if (str_contains($format, ':') === false) {
            return $format;
        }

        foreach ($options as $key => $val) {
            if (in_array($key, ['page', 'per_page'], true)) {
                continue;
            }

            /** @var string $val * */
            $format = str_replace(':' . $key, $val, $format);
        }

        return $format;
    }

    /**
     * Helper function to make sure that the $options passed makeRequest()
     * contains the required options listed in the endpoints options.
     *
     * @param array<int, string>        $endpointOptions
     * @param array<string, int|string> $options
     *
     * @throws InvalidArgumentException
     */
    private static function verifyEndpointOptions(array $endpointOptions, array $options): void
    {
        foreach ($endpointOptions as $endpointOption) {
            if (!isset($options[$endpointOption])) {
                throw new InvalidArgumentException(
                    '$options has not specified all required parameters. Parameters needed: ' . implode(', ', $endpointOptions)
                );
            }
        }
    }

    /**
     * Processes the additional parameters that can be used by the search endpoint.
     *
     * @param array<string, int|string> $options
     *
     * @return array<string, int|string>
     */
    private static function searchAdditionalParams(array $options): array
    {
        $additionalParams = [];

        foreach (['languages', 'licenses', 'keywords', 'platforms'] as $option) {
            if (isset($options[$option])) {
                $additionalParams[$option] = $options[$option];
            }
        }

        return $additionalParams;
    }

    /**
     * Verifies that the provided sort option is a valid one that libraries.io's API supports.
     */
    private static function searchVerifySortOption(string $sort): string
    {
        static $sortOptions = [
            'rank', 'stars', 'dependents_count',
            'dependent_repos_count', 'latest_release_published_at',
            'contributions_count', 'created_at',
        ];

        if (!in_array($sort, $sortOptions, true)) {
            return 'rank';
        }

        return $sort;
    }

    /**
     * Returns the jSON data as-is from the API.
     *
     * @param ResponseInterface $response The response object from makeRequest()
     */
    public function raw(ResponseInterface $response): string
    {
        return $response->getBody()->getContents();
    }

    /**
     * Decodes the jSON returned from the API. Returns as an associative array.
     *
     * @param ResponseInterface $response The response object from makeRequest()
     *
     * @return array<mixed>
     *
     * @throws JsonException
     */
    public function toArray(ResponseInterface $response): array
    {
        /** @var array<mixed> $json * */
        $json = json_decode($this->raw($response), true, flags: JSON_THROW_ON_ERROR);

        return $json;
    }

    /**
     * Decodes the jSON returned from the API. Returns as an array of objects.
     *
     * @param ResponseInterface $response The response object from makeRequest()
     *
     * @throws JsonException
     */
    public function toObject(ResponseInterface $response): stdClass
    {
        /** @var stdClass $json * */
        $json = json_decode($this->raw($response), false, flags: JSON_THROW_ON_ERROR);

        return $json;
    }
}
