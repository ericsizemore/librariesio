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
namespace Esi\LibrariesIO\Project;

use InvalidArgumentException;
use Esi\LibrariesIO\Exception\RateLimitExceededException;
use Esi\LibrariesIO\AbstractBase;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use function in_array;

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
final class Search extends AbstractBase
{
    /**
     * Processes the additional parameters that can be used by the search endpoint.
     *
     * @param array<string, int|string> $options
     * @return array<string, int|string>
     */
    protected function processAdditionalParams(array $options): array
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
    protected function verifySortOption(string $sort): string
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
     * {@inheritdoc}
     */
    public function makeRequest(array $options): ResponseInterface
    {
        // We need two options
        if (!isset($options['query'], $options['sort'])) {
            throw new InvalidArgumentException('$options requires 2 parameters (query, sort)');
        }

        // Build query
        $query = [
            'q'        => $options['query'],
            'sort'     => $this->verifySortOption(/** @phpstan-ignore-line **/$options['sort']),
            // Using pagination?
            'page'     => $options['page'] ?? 1,
            'per_page' => $options['per_page'] ?? 30
        ];

        $additionalParams = $this->processAdditionalParams($options);

        if ($additionalParams !== []) {
            $query += $additionalParams;
        }

        parent::makeClient($query);

        // Attempt the request
        try {
            return $this->client->get('search');
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 429) {
                throw new RateLimitExceededException('Libraries.io API rate limit exceeded.', previous: $e);
            } else {
                throw $e;
            }
        }
    }
}
