<?php

declare(strict_types=1);

/**
 * LibrariesIO - A simple API wrapper/client for the Libraries.io API.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @package   LibrariesIO
 * @link      https://www.secondversion.com/
 * @version   1.1.0
 * @copyright (C) 2023 Eric Sizemore
 * @license   The MIT License (MIT)
 */
namespace Esi\LibrariesIO;

// Exceptions and Attributes
use GuzzleHttp\Exception\{
    GuzzleException,
    ClientException
};
use Esi\LibrariesIO\Exception\RateLimitExceededException;
use InvalidArgumentException, JsonException;
use SensitiveParameter;

// HTTP
use GuzzleHttp\{
    Client,
    HandlerStack
};
use Psr\Http\Message\ResponseInterface;

// Cache
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Kevinrob\GuzzleCache\{
    CacheMiddleware,
    Strategy\PrivateCacheStrategy,
    Storage\Psr6CacheStorage
};

use stdClass;

// Functions and constants
use function is_dir, is_writable, json_decode, preg_match;
use function str_contains, in_array, implode;

use const JSON_THROW_ON_ERROR;

/**
 * LibrariesIO - A simple API wrapper/client for the Libraries.io API.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @package   LibrariesIO
 * @link      https://www.secondversion.com/
 * @version   1.1.0
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
class LibrariesIO
{
    /**
     * GuzzleHttp Client
     *
     * @var ?Client
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
     * @access protected
     * @param  array<string, int|string> $query
     * @return Client
     */
    protected function makeClient(?array $query = null): Client
    {
        if ($this->client !== null) {
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
     * @param string $endpoint
     * @param string $method
     * @return ResponseInterface
     * @throws ClientException|GuzzleException
     */
    protected function makeRequest(string $endpoint, string $method = 'get'): ResponseInterface
    {
        // Attempt the request
        try {
            return match($method) {
                'get'    => $this->client->get($endpoint),    /** @phpstan-ignore-line **/
                'post'   => $this->client->post($endpoint),   /** @phpstan-ignore-line **/
                'put'    => $this->client->put($endpoint),    /** @phpstan-ignore-line **/
                'delete' => $this->client->delete($endpoint), /** @phpstan-ignore-line **/
                default  => $this->client->get($endpoint)     /** @phpstan-ignore-line **/
            };
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 429) {
                throw new RateLimitExceededException('Libraries.io API rate limit exceeded.', previous: $e);
            }
            throw $e;
        }
    }

    /**
     * Performs a request to the 'platforms' endpoint.
     *
     * @param string $endpoint
     * @return ResponseInterface
     * @throws InvalidArgumentException|ClientException|GuzzleException
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
     * @param string $endpoint
     * @param array<string, int|string> $options
     * @return ResponseInterface
     * @throws InvalidArgumentException|ClientException|GuzzleException
     */
    public function project(string $endpoint, array $options): ResponseInterface
    {
        // Make sure we have the format and options for $endpoint
        $endpointParameters = $this->endpointParameters('project', $endpoint);

        if ($endpointParameters === []) {
            throw new InvalidArgumentException(
                'Invalid endpoint specified. Must be one of: contributors, dependencies, dependent_repositories, dependents, search, sourcerank, or project'
            );
        }

        /** @var array<int, string> $endpointOptions **/        
        $endpointOptions = $endpointParameters['options'];

        if (!$this->verifyEndpointOptions($endpointOptions, $options)) {
            throw new InvalidArgumentException(
                '$options has not specified all required parameters. Parameters needed: ' . implode(', ', $endpointOptions)
            );
        }

        // Build query
        $query = [
            'page'     => $options['page'] ?? 1,
            'per_page' => $options['per_page'] ?? 30
        ];

        // If on the 'search' endpoint, we have to provide the query and sort parameters.
        if ($endpoint === 'search') {
            $query += [
                'q'    => $options['query'],
                'sort' => $this->searchVerifySortOption(/** @phpstan-ignore-line **/$options['sort']),
            ];

            // Search can also have: 'languages', 'licenses', 'keywords', 'platforms' as additional parameters
            $additionalParams = $this->searchAdditionalParams($options);

            if ($additionalParams !== []) {
                $query += $additionalParams;
            }
        }

        // Build the client
        $this->makeClient($query);

        // Attempt the request
        $endpointParameters['format'] = $this->processEndpointFormat(/** @phpstan-ignore-line **/$endpointParameters['format'], $options);

        return $this->makeRequest($endpointParameters['format']);
    }

    /**
     * Performs a request to the 'repository' endpoint and a subset endpoint, which can be:
     * dependencies, projects, or repository
     *
     * @param string $endpoint
     * @param array<string, int|string> $options
     * @return ResponseInterface
     * @throws InvalidArgumentException|ClientException|GuzzleException
     */
    public function repository(string $endpoint, array $options): ResponseInterface
    {
        // Make sure we have the format and options for $endpoint
        $endpointParameters = $this->endpointParameters('repository', $endpoint);

        if ($endpointParameters === []) {
            throw new InvalidArgumentException(
                'Invalid endpoint specified. Must be one of: dependencies, projects, or repository'
            );
        }

        /** @var array<int, string> $endpointOptions **/
        $endpointOptions = $endpointParameters['options'];

        if (!$this->verifyEndpointOptions($endpointOptions, $options)) {
            throw new InvalidArgumentException(
                '$options has not specified all required parameters. Parameters needed: ' . implode(', ', $endpointOptions)
            );
        }

        // Build query
        $this->makeClient([
            // Using pagination?
            'page' => $options['page'] ?? 1,
            'per_page' => $options['per_page'] ?? 30
        ]);

        // Attempt the request
        $endpointParameters['format'] = $this->processEndpointFormat(/** @phpstan-ignore-line **/$endpointParameters['format'], $options);

        return $this->makeRequest($endpointParameters['format']);
    }

    /**
     * Performs a request to the 'user' endpoint and a subset endpoint, which can be:
     * dependencies, package_contributions, packages, repositories, repository_contributions, or subscriptions
     *
     * @param string $endpoint
     * @param array<string, int|string> $options
     * @return ResponseInterface
     * @throws InvalidArgumentException|ClientException|GuzzleException
     */
    public function user(string $endpoint, array $options): ResponseInterface
    {
        // Make sure we have the format and options for $endpoint
        $endpointParameters = $this->endpointParameters('user', $endpoint);

        if ($endpointParameters === []) {
            throw new InvalidArgumentException(
                'Invalid endpoint specified. Must be one of: dependencies, package_contributions, packages, repositories, repository_contributions, or subscriptions'
            );
        }

        /** @var array<int, string> $endpointOptions **/
        $endpointOptions = $endpointParameters['options'];

        if (!$this->verifyEndpointOptions($endpointOptions, $options)) {
            throw new InvalidArgumentException(
                '$options has not specified all required parameters. Parameters needed: ' . implode(', ', $endpointOptions)
                . '. (login can be: username or username/repo) depending on the endpoint)'
            );
        }

        // Build query
        $this->makeClient([
            'page' => $options['page'] ?? 1,
            'per_page' => $options['per_page'] ?? 30
        ]);

        // Attempt the request
        $endpointParameters['format'] = $this->processEndpointFormat(/** @phpstan-ignore-line **/$endpointParameters['format'], $options);

        return $this->makeRequest($endpointParameters['format']);
    }

    /**
     * Performs a request to the 'subscription' endpoint and a subset endpoint, which can be:
     * subscribe, check, update, unsubscribe
     *
     * @param string $endpoint
     * @param array<string, int|string> $options
     * @return ResponseInterface
     * @throws InvalidArgumentException|ClientException|GuzzleException
     */
    public function subscription(string $endpoint, array $options): ResponseInterface
    {
        // Make sure we have the format and options for $endpoint
        $endpointParameters = $this->endpointParameters('subscription', $endpoint);

        if ($endpointParameters === []) {
            throw new InvalidArgumentException(
                'Invalid endpoint specified. Must be one of: subscribe, check, update, or unsubscribe'
            );
        }

        /** @var array<int, string> $endpointOptions **/
        $endpointOptions = $endpointParameters['options'];

        if (!$this->verifyEndpointOptions($endpointOptions, $options)) {
            throw new InvalidArgumentException(
                '$options has not specified all required parameters. Parameters needed: ' . implode(', ', $endpointOptions)
            );
        }

        // Build query
        if (isset($options['include_prerelease'])) {
            $query = ['include_prerelease' => $options['include_prerelease']];
        }

        $this->makeClient($query ?? []);

        // Attempt the request
        $method = match($endpoint) {
            'subscribe'   => 'post',
            'check'       => 'get',
            'update'      => 'put',
            'unsubscribe' => 'delete',
            default       => 'get'
        };

        $endpointParameters['format'] = $this->processEndpointFormat(/** @phpstan-ignore-line **/$endpointParameters['format'], $options);

        return $this->makeRequest($endpointParameters['format'], $method);
    }

    /**
     * Processes the available parameters for a given endpoint.
     *
     * @param string $endpoint
     * @param string $subset
     * @return array<string, array<string>|string>
     */
    protected function endpointParameters(string $endpoint, string $subset): array
    {
        static $projectParameters = [
            'contributors'           => ['format' => ':platform/:name/contributors'          , 'options' => ['platform', 'name']],
            'dependencies'           => ['format' => ':platform/:name/:version/dependencies' , 'options' => ['platform', 'name', 'version']],
            'dependent_repositories' => ['format' => ':platform/:name/dependent_repositories', 'options' => ['platform', 'name']],
            'dependents'             => ['format' => ':platform/:name/dependents'            , 'options' => ['platform', 'name']],
            'search'                 => ['format' => 'search'                                , 'options' => ['query', 'sort']],
            'sourcerank'             => ['format' => ':platform/:name/sourcerank'            , 'options' => ['platform', 'name']],
            'project'                => ['format' => ':platform/:name'                       , 'options' => ['platform', 'name']]
        ];

        static $repositoryParameters = [
            'dependencies' => ['format' => 'github/:owner/:name/dependencies', 'options' => ['owner', 'name']],
            'projects'     => ['format' => 'github/:owner/:name/projects'    , 'options' => ['owner', 'name']],
            'repository'   => ['format' => 'github/:owner/:name'             , 'options' => ['owner', 'name']]
        ];

        static $userParameters = [
            'dependencies'             => ['format' => 'github/:login/dependencies'            , 'options' => ['login']],
            'package_contributions'    => ['format' => 'github/:login/project-contributions'   , 'options' => ['login']],
            'packages'                 => ['format' => 'github/:login/projects'                , 'options' => ['login']],
            'repositories'             => ['format' => 'github/:login/repositories'            , 'options' => ['login']],
            'repository_contributions' => ['format' => 'github/:login/repository-contributions', 'options' => ['login']],
            'subscriptions'            => ['format' => 'subscriptions'                         , 'options' => []],
            'user'                     => ['format' => 'github/:login'                         , 'options' => ['login']]
        ];

        static $subscriptionParameters = [
            'subscribe'   => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name', 'include_prerelease']],
            'check'       => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name']],
            'update'      => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name', 'include_prerelease']],
            'unsubscribe' => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name']]
        ];

        return match($endpoint) {
            'project'      => $projectParameters[$subset] ?? [],
            'repository'   => $repositoryParameters[$subset] ?? [],
            'user'         => $userParameters[$subset] ?? [],
            'subscription' => $subscriptionParameters[$subset] ?? [],
            default        => []
        };
    }

    /**
     * Each endpoint class will have a 'subset' of endpoints that fall under it. This 
     * function handles returning a formatted endpoint for the Client.
     *
     * @param string $format
     * @param array<string, int|string> $options
     * @return string
     */
    protected function processEndpointFormat(string $format, array $options): string
    {
        if (str_contains($format, ':') === false) {
            return $format;
        }

        foreach ($options as $key => $val) {
            if ($key === 'page' || $key === 'per_page') {
                continue;
            }
            /** @var string $val **/
            $format = str_replace(":$key", $val, $format);
        }
        return $format;
    }

    /**
     * Helper function to make sure that the $options passed to the child class' makeRequest() 
     * contains the required options listed in the endpoints options.
     *
     * @param array<int, string>        $endpointOptions
     * @param array<string, int|string> $options
     * @return bool
     */
    protected function verifyEndpointOptions(array $endpointOptions, array $options): bool
    {
        $noError = true;

        foreach ($endpointOptions as $endpointOption) {
            if (!isset($options[$endpointOption])) {
                $noError = false;
                break;
            }
        }
        return $noError;
    }

    /**
     * Processes the additional parameters that can be used by the search endpoint.
     *
     * @param array<string, int|string> $options
     * @return array<string, int|string>
     */
    protected function searchAdditionalParams(array $options): array
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
     *
     * @param string $sort
     * @return string
     */
    protected function searchVerifySortOption(string $sort): string
    {
        static $sortOptions = [
            'rank', 'stars', 'dependents_count', 
            'dependent_repos_count', 'latest_release_published_at', 
            'contributions_count', 'created_at'
        ];

        if (!in_array($sort, $sortOptions, true)) {
            $sort = 'rank';
        }
        return $sort;
    }

    /**
     * Returns the jSON data as-is from the API.
     *
     * @param ResponseInterface $response The response object from makeRequest()
     * @return string
     */
    public function raw(ResponseInterface $response): string
    {
        return $response->getBody()->getContents();
    }

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
        $json = json_decode($this->raw($response), true, flags: JSON_THROW_ON_ERROR);

        return $json;
    }

    /**
     * Decodes the jSON returned from the API. Returns as an array of objects.
     *
     * @param ResponseInterface $response The response object from makeRequest()
     * @return stdClass
     * @throws JsonException
     */
    public function toObject(ResponseInterface $response): stdClass
    {
        /** @var stdClass $json **/
        $json = json_decode($this->raw($response), false, flags: JSON_THROW_ON_ERROR);
        
        return $json;
    }
}
