<?php

declare(strict_types=1);

/**
 * This file is part of Esi\LibrariesIO.
 *
 * (c) 2023-2026 Eric Sizemore <https://github.com/ericsizemore>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Esi\LibrariesIO\Exception;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

use function vsprintf;

/**
 * RateLimitExceededException.
 */
final class RateLimitExceededException extends RuntimeException
{
    public function __construct(private readonly ClientException $clientException)
    {
        $request = $clientException->getRequest();

        $message = vsprintf(
            'Libraries.io Rate Limit Exceeded. Limit: %s, Remaining: %s, Reset: %s',
            [
                $request->getHeaderLine('x-ratelimit-limit'),
                $request->getHeaderLine('x-ratelimit-remaining'),
                $request->getHeaderLine('x-ratelimit-reset'),
            ]
        );

        parent::__construct($message, $clientException->getCode(), $clientException);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->clientException->getResponse();
    }
}
