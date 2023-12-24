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
use Esi\LibrariesIO\Exception\RateLimitExceededException;
use SensitiveParameter;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

use function implode;

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
final class Repository extends AbstractBase
{
    /**
     * {@inheritdoc}
     */
    public function __construct(#[SensitiveParameter] string $apiKey, ?string $cachePath = null)
    {
        parent::__construct($apiKey, $cachePath);
    }

    /**
     * {@inheritdoc}
     */
    public function makeRequest(string $endpoint, array $options): ResponseInterface
    {
        // Make sure we have the format and options for $endpoint
        $endpointParameters = $this->endpointParameters($endpoint);

        if ($endpointParameters === []) {
            throw new InvalidArgumentException(
                'Invalid endpoint specified. Must be one of: dependencies, projects, or repository'
            );
        }

        /** @var array<int, string> $endpointOptions **/
        $endpointOptions = $endpointParameters['options'];

        if (!parent::verifyEndpointOptions($endpointOptions, $options)) {
            throw new InvalidArgumentException(
                '$options has not specified all required parameters. Parameters needed: ' . implode(', ', $endpointOptions)
            );
        }

        // Build query
        parent::makeClient([
            // Using pagination?
            'page' => $options['page'] ?? 1,
            'per_page' => $options['per_page'] ?? 30
        ]);

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
            'dependencies' => ['format' => 'github/:owner/:name/dependencies', 'options' => ['owner', 'name']],
            'projects'     => ['format' => 'github/:owner/:name/projects'    , 'options' => ['owner', 'name']],
            'repository'   => ['format' => 'github/:owner/:name'             , 'options' => ['owner', 'name']],
            default        => []
        };
    }
}
