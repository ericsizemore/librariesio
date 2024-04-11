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
use Esi\LibrariesIO\Exception\InvalidEndpointException;
use Esi\LibrariesIO\Exception\RateLimitExceededException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use SensitiveParameter;

/**
 * @see Tests\LibrariesIOTest
 *
 * @psalm-api
 */
class LibrariesIO extends AbstractClient
{
    /**
     * @param null|array<string, mixed> $clientOptions
     *
     * @throws InvalidApiKeyException
     */
    public function __construct(#[SensitiveParameter] string $apiKey, ?string $cachePath = null, ?array $clientOptions = null)
    {
        parent::__construct($apiKey, $cachePath, $clientOptions);
    }

    /**
     * @throws ClientException
     * @throws GuzzleException
     * @throws RateLimitExceededException
     */
    public function platform(): ResponseInterface
    {
        // The only valid endpoint is 'platforms' currently
        return $this->request('GET', 'platforms');
    }

    /**
     * Performs a request to the 'project' endpoint and a subset endpoint, which can be:
     * contributors, dependencies, dependent_repositories, dependents, search, sourcerank, or project
     *
     * @param array<array-key, int|string> $options
     *
     * @throws InvalidEndpointException
     * @throws ClientException
     * @throws GuzzleException
     * @throws RateLimitExceededException
     */
    public function project(string $endpoint, array $options): ResponseInterface
    {
        [$endpointFormat, $requestMethod] = Utils::endpointParameters('project', $endpoint, $options);

        $options = Utils::validatePagination($options);

        $query = [
            'page'     => $options['page'],
            'per_page' => $options['per_page'],
        ];

        // If on the 'search' endpoint, we have to provide the query and sort parameters.
        if ($endpoint === 'search') {
            $query += [
                'q'    => $options['query'],
                'sort' => Utils::searchVerifySortOption((string) $options['sort']),
            ];
            $query += Utils::searchAdditionalParams($options);
        }

        $query = ['query' => $query];

        return $this->request($requestMethod, $endpointFormat, $query);
    }

    /**
     * Performs a request to the 'repository' endpoint and a subset endpoint, which can be:
     * dependencies, projects, or repository
     *
     * @param array<array-key, int|string> $options
     *
     * @throws InvalidEndpointException
     * @throws ClientException
     * @throws GuzzleException
     * @throws RateLimitExceededException
     */
    public function repository(string $endpoint, array $options): ResponseInterface
    {
        [$endpointFormat, $requestMethod] = Utils::endpointParameters('repository', $endpoint, $options);

        $options = Utils::validatePagination($options);

        $query = [
            'query' => [
                'page'     => $options['page'],
                'per_page' => $options['per_page'],
            ],
        ];

        return $this->request($requestMethod, $endpointFormat, $query);
    }

    /**
     * Performs a request to the 'subscription' endpoint and a subset endpoint, which can be:
     * subscribe, check, update, unsubscribe
     *
     * @param array<array-key, int|string> $options
     *
     * @throws InvalidEndpointException
     * @throws ClientException
     * @throws GuzzleException
     * @throws RateLimitExceededException
     */
    public function subscription(string $endpoint, array $options): ResponseInterface
    {
        [$endpointFormat, $requestMethod] = Utils::endpointParameters('subscription', $endpoint, $options);

        $query = [];

        if (isset($options['include_prerelease'])) {
            $query = ['query' => ['include_prerelease' => $options['include_prerelease']]];
        }

        return $this->request($requestMethod, $endpointFormat, $query);
    }

    /**
     * Performs a request to the 'user' endpoint and a subset endpoint, which can be:
     * dependencies, package_contributions, packages, repositories, repository_contributions, or subscriptions
     *
     * @param array<array-key, int|string> $options
     *
     * @throws InvalidEndpointException
     * @throws ClientException
     * @throws GuzzleException
     * @throws RateLimitExceededException
     */
    public function user(string $endpoint, array $options): ResponseInterface
    {
        [$endpointFormat, $requestMethod] = Utils::endpointParameters('user', $endpoint, $options);

        $options = Utils::validatePagination($options);

        $query = [
            'query' => [
                'page'     => $options['page'],
                'per_page' => $options['per_page'],
            ],
        ];

        return $this->request($requestMethod, $endpointFormat, $query);
    }
}
