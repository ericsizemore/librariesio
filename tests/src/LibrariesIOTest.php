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

namespace Esi\LibrariesIO\Tests;

use Esi\LibrariesIO\AbstractClient;
use Esi\LibrariesIO\Exception\InvalidApiKeyException;
use Esi\LibrariesIO\Exception\InvalidEndpointException;
use Esi\LibrariesIO\Exception\InvalidEndpointOptionsException;
use Esi\LibrariesIO\Exception\RateLimitExceededException;
use Esi\LibrariesIO\LibrariesIO;
use Esi\LibrariesIO\Utils;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

use function sys_get_temp_dir;

/**
 * LibrariesIO Tests.
 *
 * @internal
 *
 * @psalm-internal Esi\LibrariesIO\Tests
 */
#[CoversClass(LibrariesIO::class)]
#[CoversClass(AbstractClient::class)]
#[CoversClass(Utils::class)]
#[CoversClass(RateLimitExceededException::class)]
final class LibrariesIOTest extends TestCase
{
    /**
     * @var array<string, ClientException|Response>
     */
    private array $responses;

    private string $testApiKey;

    #[\Override]
    protected function setUp(): void
    {
        $rateLimitHeaders = [
            'X-RateLimit-Limit'     => '60',
            'X-RateLimit-Remaining' => '0',
            'X-RateLimit-Reset'     => '',
        ];

        $this->testApiKey = md5('test');

        $this->responses = [
            'valid'       => new Response(200, body: '{"Hello":"World"}'),
            'clientError' => new ClientException('Error Communicating with Server', new Request('GET', 'test'), new Response(202, ['X-Foo' => 'Bar'])),
            'rateLimit'   => new ClientException('Rate Limit Exceeded', new Request('GET', 'test'), new Response(429, $rateLimitHeaders)),
            'rateLimiter' => new Response(429, $rateLimitHeaders, 'Rate Limit Exceeded'),
        ];
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function dataEndpointProvider(): Iterator
    {
        yield ['project', '/project', 'https://libraries.io/api/'];
        yield ['project', 'project', 'https://libraries.io/api/'];
        yield ['/project', '/project', 'https://libraries.io/api'];
        yield ['/project', 'project', 'https://libraries.io/api'];
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function dataMethodProvider(): Iterator
    {
        yield ['GET', 'GET'];
        yield ['POST', 'POST'];
        yield ['DELETE', 'DELETE'];
        yield ['PUT', 'PUT'];
        yield ['GET', 'PATCH'];
        yield ['GET', 'OPTIONS'];
        yield ['GET', 'HEAD'];
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function dataProjectProvider(): Iterator
    {
        yield ['{"Hello":"World"}', 'contributors', ['platform' => 'npm', 'name' => 'utility']];
        yield ['{"Hello":"World"}', 'dependencies', ['platform' => 'npm', 'name' => 'utility', 'version' => 'latest']];
        yield ['{"Hello":"World"}', 'dependent_repositories', ['platform' => 'npm', 'name' => 'utility']];
        yield ['{"Hello":"World"}', 'dependents', ['platform' => 'npm', 'name' => 'utility']];
        yield ['{"Hello":"World"}', 'search', ['query' => 'grunt', 'sort' => 'rank', 'keywords' => 'wordpress']];
        yield ['{"Hello":"World"}', 'search', ['query' => 'grunt', 'sort' => 'notvalid', 'keywords' => 'wordpress']];
        yield ['{"Hello":"World"}', 'sourcerank', ['platform' => 'npm', 'name' => 'utility']];
        yield ['{"Hello":"World"}', 'project', ['platform' => 'npm', 'name' => 'utility', 'page' => 1, 'per_page' => 30]];
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function dataRepositoryProvider(): Iterator
    {
        yield ['{"Hello":"World"}', 'dependencies', ['owner' => 'ericsizemore', 'name' => 'utility']];
        yield ['{"Hello":"World"}', 'projects', ['owner' => 'ericsizemore', 'name' => 'utility']];
        yield ['{"Hello":"World"}', 'repository', ['owner' => 'ericsizemore', 'name' => 'utility']];
        yield ['{"Hello":"World"}', 'dependencies', ['owner' => 'ericsizemore', 'name' => 'utility']];
        yield ['{"Hello":"World"}', 'projects', ['owner' => 'ericsizemore', 'name' => 'utility', 'page' => 1, 'per_page' => 30]];
        yield ['{"Hello":"World"}', 'repository', ['owner' => 'ericsizemore', 'name' => 'utility']];
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function dataSubscriptionProvider(): Iterator
    {
        yield ['{"Hello":"World"}', 'subscribe', ['platform' => 'npm', 'name' => 'utility', 'include_prerelease' => 'true']];
        yield ['{"Hello":"World"}', 'check', ['platform' => 'npm', 'name' => 'utility']];
        yield ['{"Hello":"World"}', 'update', ['platform' => 'npm', 'name' => 'utility', 'include_prerelease' => 'false']];
        yield ['{"Hello":"World"}', 'unsubscribe', ['platform' => 'npm', 'name' => 'utility']];
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function dataUserProvider(): Iterator
    {
        yield ['{"Hello":"World"}', 'dependencies', ['login' => 'ericsizemore']];
        yield ['{"Hello":"World"}', 'package_contributions', ['login' => 'ericsizemore']];
        yield ['{"Hello":"World"}', 'packages', ['login' => 'ericsizemore']];
        yield ['{"Hello":"World"}', 'repositories', ['login' => 'ericsizemore']];
        yield ['{"Hello":"World"}', 'repository_contributions', ['login' => 'ericsizemore', 'page' => 1, 'per_page' => 30]];
        yield ['{"Hello":"World"}', 'subscriptions', []];
    }

    #[TestDox('Client error throws a Guzzle ClientException')]
    public function testClientError(): void
    {
        $mockHandler = new MockHandler([$this->responses['clientError']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $this->expectException(ClientException::class);
        $mockClient->platform();
    }

    #[TestDox('Providing an invalid API key results in an InvalidApiKeyException')]
    public function testInvalidApiKey(): void
    {
        $this->expectException(InvalidApiKeyException::class);
        $this->mockClient('notvalid');
    }

    #[DataProvider('dataEndpointProvider')]
    public function testNormalizeEndpoint(string $expected, string $endpoint, string $apiUrl): void
    {
        self::assertSame($expected, Utils::normalizeEndpoint($endpoint, $apiUrl));
    }

    #[DataProvider('dataMethodProvider')]
    public function testNormalizeMethod(string $expected, string $method): void
    {
        self::assertSame($expected, Utils::normalizeMethod($method));
    }

    #[TestDox('LibrariesIO::platform() returns expected response')]
    public function testPlatform(): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $response    = $mockClient->platform();

        self::assertInstanceOf(Response::class, $response);
        self::assertSame('{"Hello":"World"}', $response->getBody()->getContents());
    }

    /**
     * @param array<string, int|string> $options
     */
    #[DataProvider('dataProjectProvider')]
    #[TestDox('LibrariesIO::project() returns expected response')]
    public function testProject(string $expected, string $endpoint, array $options): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $response    = $mockClient->project($endpoint, $options);

        self::assertInstanceOf(Response::class, $response);
        self::assertSame($expected, $response->getBody()->getContents());
    }

    #[TestDox('LibrariesIO::project() with an invalid endpoint throws an InvalidEndpointException')]
    public function testProjectInvalidEndpoint(): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);

        $this->expectException(InvalidEndpointException::class);
        $mockClient->project('notvalid', ['platform' => 'npm', 'name' => 'utility']);
    }

    #[TestDox('LibrariesIO::project() with an invalid options throws an InvalidEndpointOptionsException')]
    public function testProjectInvalidOptions(): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);

        $this->expectException(InvalidEndpointOptionsException::class);
        $mockClient->project('search', ['huh' => 'what']);
    }

    #[TestDox('A response with status code 429 throws a RateLimitExceededException')]
    public function testRateLimitExceeded(): void
    {
        $response    = $this->responses['rateLimiter'];
        $mockHandler = new MockHandler([$response]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);

        try {
            $mockClient->platform();
        } catch (RateLimitExceededException $rateLimitExceededException) {
            $response = $rateLimitExceededException->getResponse();

            self::assertSame('60', $response->getHeaderLine('x-ratelimit-limit'));
            self::assertSame('0', $response->getHeaderLine('x-ratelimit-remaining'));
            self::assertSame('', $response->getHeaderLine('x-ratelimit-reset'));
        }
    }

    #[TestDox('(ClientException) A response with status code 429 throws a RateLimitExceededException')]
    public function testRateLimitExceededClientException(): void
    {
        $response    = $this->responses['rateLimit'];
        $mockHandler = new MockHandler([$response]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);

        try {
            $mockClient->platform();
        } catch (RateLimitExceededException $rateLimitExceededException) {
            $response = $rateLimitExceededException->getResponse();

            self::assertSame('60', $response->getHeaderLine('x-ratelimit-limit'));
            self::assertSame('0', $response->getHeaderLine('x-ratelimit-remaining'));
            self::assertSame('', $response->getHeaderLine('x-ratelimit-reset'));
        }
    }

    #[TestDox('Utils::toRaw() returns raw JSON and expected response')]
    public function testRaw(): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $response    = $mockClient->user('dependencies', ['login' => 'ericsizemore']);

        self::assertInstanceOf(Response::class, $response);
        self::assertSame('{"Hello":"World"}', Utils::raw($response));
    }

    /**
     * @param array<string, int|string> $options
     */
    #[DataProvider('dataRepositoryProvider')]
    #[TestDox('LibrariesIO::repository() returns expected response')]
    public function testRepository(string $expected, string $endpoint, array $options): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $response    = $mockClient->repository($endpoint, $options);

        self::assertInstanceOf(Response::class, $response);
        self::assertSame($expected, $response->getBody()->getContents());
    }

    /**
     * Test the repository endpoint with an invalid $endpoint arg specified.
     */
    public function testRepositoryInvalidEndpoint(): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $this->expectException(InvalidEndpointException::class);
        $mockClient->repository('notvalid', ['owner' => 'ericsizemore', 'name' => 'utility']);
    }

    /**
     * Test the repository endpoint with a valid subset $endpoint arg and invalid options specified.
     */
    public function testRepositoryInvalidOptions(): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $this->expectException(InvalidArgumentException::class);
        $mockClient->repository('repository', ['huh' => 'what']);
    }

    /**
     * Test the subscription endpoint.
     *
     * @param array<string> $options
     */
    #[DataProvider('dataSubscriptionProvider')]
    public function testSubscription(string $expected, string $endpoint, array $options): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $response    = $mockClient->subscription($endpoint, $options);

        self::assertInstanceOf(Response::class, $response);
        self::assertSame($expected, $response->getBody()->getContents());
    }

    /**
     * Test the subscription endpoint with an invalid $endpoint arg specified.
     */
    public function testSubscriptionInvalidEndpoint(): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $this->expectException(InvalidEndpointException::class);
        $mockClient->subscription('notvalid', ['platform' => 'npm', 'name' => 'utility']);
    }

    /**
     * Test the subscription endpoint with a valid $endpoint arg and invalid $options specified.
     */
    public function testSubscriptionInvalidOptions(): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $this->expectException(InvalidEndpointOptionsException::class);
        $mockClient->subscription('check', ['huh' => 'what']);
    }

    /**
     * Test the toArray function. It decodes the raw json data into an associative array.
     */
    public function testToArray(): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $response    = $mockClient->user('dependencies', ['login' => 'ericsizemore']);

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(['Hello' => 'World'], Utils::toArray($response));
    }

    /**
     * Test the toObject function. It decodes the raw json data and creates a \stdClass object.
     */
    public function testToObject(): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $response    = $mockClient->user('dependencies', ['login' => 'ericsizemore']);

        self::assertInstanceOf(Response::class, $response);

        $expected        = new stdClass();
        $expected->Hello = 'World';

        self::assertEquals($expected, Utils::toObject($response));
    }

    /**
     * Test the user endpoint.
     *
     * @param array<string, int|string> $options
     */
    #[DataProvider('dataUserProvider')]
    public function testUser(string $expected, string $endpoint, array $options): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $response    = $mockClient->user($endpoint, $options);

        self::assertInstanceOf(Response::class, $response);
        self::assertSame($expected, $response->getBody()->getContents());
    }

    /**
     * Test the user endpoint with an invalid $endpoint arg specified.
     */
    public function testUserInvalidEndpoint(): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $this->expectException(InvalidEndpointException::class);
        $mockClient->user('notvalid', ['login' => 'ericsizemore']);
    }

    /**
     * Test the user endpoint with a valid $endpoint arg and invalid $options specified.
     */
    public function testUserInvalidOptions(): void
    {
        $mockHandler = new MockHandler([$this->responses['valid']]);
        $mockClient  = $this->mockClient($this->testApiKey, $mockHandler);
        $this->expectException(InvalidEndpointOptionsException::class);
        $mockClient->user('packages', ['huh' => 'what']);
    }

    /**
     * Creates a mock for testing.
     */
    private function mockClient(string $apiKey, ?MockHandler $mockHandler = null): LibrariesIO&MockObject
    {
        return $this
            ->getMockBuilder(LibrariesIO::class)
            ->setConstructorArgs([$apiKey, sys_get_temp_dir(), ['_mockHandler' => $mockHandler]])
            ->onlyMethods([])
            ->getMock();
    }
}
