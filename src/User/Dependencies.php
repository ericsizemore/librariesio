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
namespace Esi\LibrariesIO\User;

use InvalidArgumentException;
use Esi\LibrariesIO\Exception\RateLimitExceededException;
use Esi\LibrariesIO\AbstractBase;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use function sprintf;

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
final class Dependencies extends AbstractBase
{
    /**
     * {@inheritdoc}
     */
    public function makeRequest(?array $options = null): ResponseInterface
    {
        // We need one option
        if (!isset($options['login'])) {
            throw new InvalidArgumentException('$options requires 1 parameter (login). Either: username or username/repo');
        }

        // Build query
        parent::makeClient([
            // Using pagination?
            'page' => $options['page'] ?? 1,
            'per_page' => $options['per_page'] ?? 30
        ]);

        // Attempt the request
        try {
            return $this->client->get(sprintf('github/%s/dependencies', $options['login']));
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 429) {
                throw new RateLimitExceededException('Libraries.io API rate limit exceeded.', previous: $e);
            } else {
                throw $e;
            }
        }
    }
}
