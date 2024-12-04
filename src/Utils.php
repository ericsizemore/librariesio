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

use Esi\LibrariesIO\Exception\InvalidEndpointException;
use Esi\LibrariesIO\Exception\InvalidEndpointOptionsException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use stdClass;

use function implode;
use function is_dir;
use function is_writable;
use function json_decode;
use function ltrim;
use function max;
use function min;
use function str_contains;
use function str_ends_with;
use function str_replace;
use function strtoupper;

use const JSON_THROW_ON_ERROR;

/**
 * Utility class.
 */
final class Utils
{
    private const MAX_PERPAGE_ALLOWED = 100;
    private const MIN_PAGE_ALLOWED    = 1;
    private const MIN_PERPAGE_ALLOWED = 30;

    /**
     * @var array<string, array{format: string, options: array<string>|array{}, method: string}>
     */
    public static array $projectParameters = [
        'contributors'           => ['format' => ':platform/:name/contributors', 'options' => ['platform', 'name'], 'method' => 'get'],
        'dependencies'           => ['format' => ':platform/:name/:version/dependencies', 'options' => ['platform', 'name', 'version'], 'method' => 'get'],
        'dependent_repositories' => ['format' => ':platform/:name/dependent_repositories', 'options' => ['platform', 'name'], 'method' => 'get'],
        'dependents'             => ['format' => ':platform/:name/dependents', 'options' => ['platform', 'name'], 'method' => 'get'],
        'search'                 => ['format' => 'search', 'options' => ['query', 'sort'], 'method' => 'get'],
        'sourcerank'             => ['format' => ':platform/:name/sourcerank', 'options' => ['platform', 'name'], 'method' => 'get'],
        'project'                => ['format' => ':platform/:name', 'options' => ['platform', 'name'], 'method' => 'get'],
    ];

    /**
     * @var array<string, array{format: string, options: array<string>|array{}, method: string}>
     */
    public static array $repositoryParameters = [
        'dependencies' => ['format' => 'github/:owner/:name/dependencies', 'options' => ['owner', 'name'], 'method' => 'get'],
        'projects'     => ['format' => 'github/:owner/:name/projects', 'options' => ['owner', 'name'], 'method' => 'get'],
        'repository'   => ['format' => 'github/:owner/:name', 'options' => ['owner', 'name'], 'method' => 'get'],
    ];

    /**
     * @var array<string, array{format: string, options: array<string>|array{}, method: string}>
     */
    public static array $subscriptionParameters = [
        'subscribe'   => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name', 'include_prerelease'], 'method' => 'post'],
        'check'       => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name'], 'method' => 'get'],
        'update'      => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name', 'include_prerelease'], 'method' => 'put'],
        'unsubscribe' => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name'], 'method' => 'delete'],
    ];

    /**
     * @var array<string, array{format: string, options: array<string>|array{}, method: string}>
     */
    public static array $userParameters = [
        'dependencies'             => ['format' => 'github/:login/dependencies', 'options' => ['login'], 'method' => 'get'],
        'package_contributions'    => ['format' => 'github/:login/project-contributions', 'options' => ['login'], 'method' => 'get'],
        'packages'                 => ['format' => 'github/:login/projects', 'options' => ['login'], 'method' => 'get'],
        'repositories'             => ['format' => 'github/:login/repositories', 'options' => ['login'], 'method' => 'get'],
        'repository_contributions' => ['format' => 'github/:login/repository-contributions', 'options' => ['login'], 'method' => 'get'],
        'subscriptions'            => ['format' => 'subscriptions', 'options' => [], 'method' => 'get'],
        'user'                     => ['format' => 'github/:login', 'options' => ['login'], 'method' => 'get'],
    ];

    /**
     * @param array<array-key, int|string> $options
     *
     * @throws InvalidEndpointException
     * @throws InvalidEndpointOptionsException
     *
     * @return array{0: string, 1: string}
     */
    public static function endpointParameters(string $endpoint, string $subset, array $options): array
    {
        $parameters = match ($endpoint) {
            'project'      => Utils::$projectParameters[$subset] ?? null,
            'repository'   => Utils::$repositoryParameters[$subset] ?? null,
            'user'         => Utils::$userParameters[$subset] ?? null,
            'subscription' => Utils::$subscriptionParameters[$subset] ?? null,
            default        => null
        };

        if ($parameters === null) {
            throw new InvalidEndpointException('Invalid endpoint subset specified.');
        }

        foreach ($parameters['options'] as $endpointOption) {
            if (!isset($options[$endpointOption])) {
                throw new InvalidEndpointOptionsException(
                    '$options has not specified all required parameters. Parameters needed: ' . implode(', ', $parameters['options'])
                );
            }
        }

        $parameters['format'] = Utils::processEndpointFormat($parameters['format'], $options);

        return [$parameters['format'], $parameters['method']];
    }

    public static function normalizeEndpoint(null|string $endpoint, string $apiUrl): string
    {
        $endpoint = ltrim($endpoint ?? '', '/');

        if (!str_ends_with($apiUrl, '/')) {
            $endpoint = '/' . $endpoint;
        }

        return $endpoint;
    }

    public static function normalizeMethod(string $method): string
    {
        $method = strtoupper($method);

        // Check for a valid method
        if (!\in_array($method, ['GET', 'DELETE', 'POST', 'PUT', ], true)) {
            $method = 'GET';
        }

        return $method;
    }

    public static function raw(ResponseInterface $response): string
    {
        return $response->getBody()->getContents();
    }

    /**
     * @param array<array-key, int|string> $options
     *
     * @return array<array-key, int|string>
     */
    public static function searchAdditionalParams(array $options): array
    {
        $additionalParams = [];

        foreach (['languages', 'licenses', 'keywords', 'platforms'] as $option) {
            if (isset($options[$option])) {
                $additionalParams[$option] = $options[$option];
            }
        }

        return $additionalParams;
    }

    public static function searchVerifySortOption(string $sort): string
    {
        /** @var array<int, string> $sortOptions */
        static $sortOptions = [
            'rank', 'stars', 'dependents_count',
            'dependent_repos_count', 'latest_release_published_at',
            'contributions_count', 'created_at',
        ];

        if (!\in_array($sort, $sortOptions, true)) {
            return 'rank';
        }

        return $sort;
    }

    /**
     * @throws JsonException
     *
     * @return array<mixed>
     */
    public static function toArray(ResponseInterface $response): array
    {
        /** @var array<mixed> $json * */
        $json = json_decode(Utils::raw($response), true, flags: JSON_THROW_ON_ERROR);

        return $json;
    }

    /**
     * @throws JsonException
     */
    public static function toObject(ResponseInterface $response): stdClass
    {
        /** @var stdClass $json * */
        $json = json_decode(Utils::raw($response), false, flags: JSON_THROW_ON_ERROR);

        return $json;
    }

    public static function validateCachePath(?string $cachePath = null): ?string
    {
        if ($cachePath === null) {
            return null;
        }

        if (!is_dir($cachePath) || !is_writable($cachePath)) {
            return null;
        }

        return $cachePath;
    }

    /**
     * @param array<array-key, int|string> $options
     *
     * @return array<array-key, int|string>
     */
    public static function validatePagination(array $options): array
    {
        if (!isset($options['page'], $options['per_page'])) {
            $options['page']     = self::MIN_PAGE_ALLOWED;
            $options['per_page'] = self::MIN_PERPAGE_ALLOWED;
        } else {
            $options['page'] = \intval($options['page']);
            $options['page'] = max(self::MIN_PAGE_ALLOWED, $options['page']);

            $options['per_page'] = \intval($options['per_page']);
            $options['per_page'] = max(self::MIN_PERPAGE_ALLOWED, $options['per_page']);
            $options['per_page'] = min(self::MAX_PERPAGE_ALLOWED, $options['per_page']);
        }

        return $options;
    }

    /**
     * Each endpoint class will have a 'subset' of endpoints that fall under it. This
     * function handles returning a formatted endpoint for the Client.
     *
     * @param array<array-key, int|string> $options
     */
    private static function processEndpointFormat(string $format, array $options): string
    {
        if (str_contains($format, ':') === false) {
            return $format;
        }

        foreach ($options as $key => $val) {
            if (\in_array($key, ['page', 'per_page'], true)) {
                continue;
            }

            /** @var string $val * */
            $format = str_replace(':' . $key, $val, $format);
        }

        return $format;
    }
}
