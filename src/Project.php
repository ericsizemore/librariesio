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

use InvalidArgumentException;

use Esi\LibrariesIO\{
    Exception\RateLimitExceededException,
    AbstractBase
};

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

use function in_array, implode;

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
final class Project extends AbstractBase
{
    /**
     * {@inheritdoc}
     */
    public function makeRequest(string $endpoint, array $options): ResponseInterface
    {
        // Make sure we have the format and options for $endpoint
        $endpointParameters = $this->endpointParameters($endpoint);

        if ($endpointParameters === []) {
            throw new InvalidArgumentException(
                'Invalid endpoint specified. Must be one of: contributors, dependencies, dependent_repositories, dependents, search, sourcerank, or project'
            );
        }

        /** @var array<int, string> $endpointOptions **/        
        $endpointOptions = $endpointParameters['options'];

        if (!parent::verifyEndpointOptions($endpointOptions, $options)) {
            throw new InvalidArgumentException(
                '$options has not specified all required parameters. Paremeters needed: ' . implode(', ', $endpointOptions)
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

            // Search can also have: 'languages', 'licenses', 'keywords', 'platforms' as additional paremeters
            $additionalParams = $this->searchAdditionalParams($options);

            if ($additionalParams !== []) {
                $query += $additionalParams;
            }
        }

        // Build the client
        parent::makeClient($query);

        // Attempt the request
        $endpointParameters['format'] = parent::processEndpointFormat(/** @phpstan-ignore-line **/$endpointParameters['format'], $options);

        try {
            return $this->client->get($endpointParameters['format']);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 429) {
                throw new RateLimitExceededException('Libraries.io API rate limit exceeded.', previous: $e);
            } else {
                throw $e;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function endpointParameters(string $endpoint): array
    {
        return match($endpoint) {
            'contributors'           => ['format' => ':platform/:name/contributors'          , 'options' => ['platform', 'name']],
            'dependencies'           => ['format' => ':platform/:name/:version/dependencies' , 'options' => ['platform', 'name', 'version']],
            'dependent_repositories' => ['format' => ':platform/:name/dependent_repositories', 'options' => ['platform', 'name']],
            'dependents'             => ['format' => ':platform/:name/dependents'            , 'options' => ['platform', 'name']],
            'search'                 => ['format' => 'search'                                , 'options' => ['query', 'sort']],
            'sourcerank'             => ['format' => ':platform/:name/sourcerank'            , 'options' => ['platform', 'name']],
            'project'                => ['format' => ':platform/:name'                       , 'options' => ['platform', 'name']],
            default                  => []
        };
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
}