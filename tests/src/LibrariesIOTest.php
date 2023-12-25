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
namespace Esi\LibrariesIO\Tests;

use Esi\LibrariesIO\LibrariesIO;
use Esi\LibrariesIO\Exception\RateLimitExceededException;

use PHPUnit\Framework\{
    TestCase,
    MockObject\MockObject,
    Attributes\CoversClass,
    Attributes\DataProvider
};

use GuzzleHttp\{
    Client,
    Handler\MockHandler,
    HandlerStack,
    Psr7\Response,
    Psr7\Request,
    Exception\RequestException,
    Exception\ClientException
};

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
class LibrariesIOTest extends TestCase
{
    /**
     * A mock'ed GuzzleHttp client we can inject for testing.
     *
     * @var Client
     */
    protected Client $client;

    /**
     * The mock/stub of the main class.
     *
     * @var LibrariesIO&MockObject
     */
    protected LibrariesIO&MockObject $stub;

    /**
     * Creates the mock to be used throughout testing.
     */
    public function setUp(): void
    {
        // Create a mock and queue two responses.
        $mock = new MockHandler([
            new Response(200, body: '{"Hello":"World"}')
        ]);
        
        $handlerStack = HandlerStack::create($mock);
        $this->client = new Client(['handler' => $handlerStack]);

        $this->stub = $this
            ->getMockBuilder(LibrariesIO::class)
            ->setConstructorArgs([md5('test'), \sys_get_temp_dir()])
            ->onlyMethods([])
            ->getMock();
    }

    /**
     * Mock a client error via Guzzle's ClientException
     */
    public function testClientError(): void
    {
        // Create a mock and queue two responses.
        $mock = new MockHandler([
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), new Response(202, ['X-Foo' => 'Bar']))
        ]);
        
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $stub = $this
            ->getMockBuilder(LibrariesIO::class)
            ->setConstructorArgs([md5('test'), \sys_get_temp_dir()])
            ->onlyMethods([])
            ->getMock();
        $this->expectException(ClientException::class);
        $stub->client = $client;
        $response = $stub->platform();
    }

    /**
     * Tests library handling of HTTP 429, which can be returned by libraries.io if rate limit
     * is exceeded.
     */
    public function testRateLimitExceeded(): void
    {
        // Create a mock and queue two responses.
        $mock = new MockHandler([
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), new Response(429, ['X-Foo' => 'Bar']))
        ]);
        
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $stub = $this
            ->getMockBuilder(LibrariesIO::class)
            ->setConstructorArgs([md5('test'), \sys_get_temp_dir()])
            ->onlyMethods([])
            ->getMock();
        $this->expectException(RateLimitExceededException::class);
        $stub->client = $client;
        $response = $stub->platform();
    }

    /**
     * Test providing an invalid API key.
     */
    public function testInvalidApiKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $stub = $this
            ->getMockBuilder(LibrariesIO::class)
            ->setConstructorArgs(['notvalid', \sys_get_temp_dir()])
            ->onlyMethods([])
            ->getMock();
    }

    /**
     * Test the platform endpoint
     */
    public function testPlatform(): void
    {
        $this->stub->client = $this->client;
        $response = $this->stub->platform();
        self::assertInstanceOf(Response::class, $response);
        self::assertEquals('{"Hello":"World"}', $response->getBody()->getContents());
    }

    /**
     * Test the platform endpoint with an invalid $endpoint arg specified.
     */
    public function testPlatformInvalid(): void
    {
        $this->stub->client = $this->client;
        $this->expectException(\InvalidArgumentException::class);
        $response = $this->stub->platform('notvalid');
    }

    /**
     * Provides testing data for the project endpoint testing.
     *
     * @return array<int, array<int, array<string, int|string>|bool|string>>
     */
    public static function dataProjectProvider(): array
    {
        return [
        //contributors, dependencies, dependent_repositories, dependents, search, sourcerank, or project
            ['{"Hello":"World"}', 'contributors'          , ['platform' => 'npm'  , 'name' => 'utility']],
            ['{"Hello":"World"}', 'dependencies'          , ['platform' => 'npm'  , 'name' => 'utility', 'version' => 'latest']],
            ['{"Hello":"World"}', 'dependent_repositories', ['platform' => 'npm'  , 'name' => 'utility']],
            ['{"Hello":"World"}', 'dependents'            , ['platform' => 'npm'  , 'name' => 'utility']],
            ['{"Hello":"World"}', 'search'                , ['query'    => 'grunt', 'sort' => 'rank', 'keywords' => 'wordpress']],
            ['{"Hello":"World"}', 'search'                , ['query'    => 'grunt', 'sort' => 'notvalid', 'keywords' => 'wordpress']],
            ['{"Hello":"World"}', 'sourcerank'            , ['platform' => 'npm'  , 'name' => 'utility']],
            ['{"Hello":"World"}', 'project'               , ['platform' => 'npm'  , 'name' => 'utility', 'page' => 1, 'per_page' => 30]]
        ];
    }

    /**
     * Tests the project endpoing
     *
     * @param string $expected
     * @param string $endpoint
     * @param array<string, int|string> $options
     */
    #[DataProvider('dataProjectProvider')] 
    public function testProject(string $expected, string $endpoint, array $options): void
    {
        $this->stub->client = $this->client;
        $response = $this->stub->project($endpoint, $options);
        self::assertInstanceOf(Response::class, $response);
        self::assertEquals($expected, $response->getBody()->getContents());
    }

    /**
     * Test the project endpoint with an invalid subset $endpoint arg specified.
     */
    public function testProjectInvalidEndpoint(): void
    {
        $this->stub->client = $this->client;
        $this->expectException(\InvalidArgumentException::class);
        $response = $this->stub->project('notvalid', ['platform' => 'npm'  , 'name' => 'utility']);
    }

    /**
     * Test the platform endpoint with an valid subset $endpoint arg and invalid $options specified.
     */
    public function testProjectInvalidOptions(): void
    {
        $this->stub->client = $this->client;
        $this->expectException(\InvalidArgumentException::class);
        $response = $this->stub->project('search', ['huh' => 'what']);
    }

    /**
     * Provides testing data for the repository endpoint.
     *
     * @return array<int, array<int, array<string, int|string>|bool|string>>
     */
    public static function dataRepositoryProvider(): array
    {
        return [
            ['{"Hello":"World"}', 'dependencies' , ['owner' => 'ericsizemore', 'name' => 'utility']],
            ['{"Hello":"World"}', 'projects'     , ['owner' => 'ericsizemore', 'name' => 'utility']],
            ['{"Hello":"World"}', 'repository'   , ['owner' => 'ericsizemore', 'name' => 'utility']],
            ['{"Hello":"World"}', 'dependencies' , ['owner' => 'ericsizemore', 'name' => 'utility']],
            ['{"Hello":"World"}', 'projects'     , ['owner' => 'ericsizemore', 'name' => 'utility', 'page' => 1, 'per_page' => 30]],
            ['{"Hello":"World"}', 'repository'   , ['owner' => 'ericsizemore', 'name' => 'utility']],
        ];
    }

    /**
     * Test the repository endpoint
     *
     * @param string $expected
     * @param string $endpoint
     * @param array<string, int|string> $options
     */
    #[DataProvider('dataRepositoryProvider')] 
    public function testRepository(string $expected, string $endpoint, array $options): void
    {
        $this->stub->client = $this->client;
        $response = $this->stub->repository($endpoint, $options);
        self::assertInstanceOf(Response::class, $response);
        self::assertEquals($expected, $response->getBody()->getContents());
    }

    /**
     * Test the repository endpoint with an invalid $endpoint arg specified.
     */
    public function testRepositoryInvalidEndpoint(): void
    {
        $this->stub->client = $this->client;
        $this->expectException(\InvalidArgumentException::class);
        $response = $this->stub->repository('notvalid', ['owner' => 'ericsizemore', 'name' => 'utility']);
    }

    /**
     * Test the repository endpoint with a valid subset $endpoint arg and invalid options specified.
     */
    public function testRepositoryInvalidOptions(): void
    {
        $this->stub->client = $this->client;
        $this->expectException(\InvalidArgumentException::class);
        $response = $this->stub->repository('repository', ['huh' => 'what']);
    }

    /**
     * Provides the data for testing the user endpoint
     *
     * @return array<int, array<int, array<string, int|string>|bool|string>>
     */
    public static function dataUserProvider(): array
    {
        return [
            ['{"Hello":"World"}', 'dependencies'            , ['login' => 'ericsizemore']],
            ['{"Hello":"World"}', 'package_contributions'   , ['login' => 'ericsizemore']],
            ['{"Hello":"World"}', 'packages'                , ['login' => 'ericsizemore']],
            ['{"Hello":"World"}', 'repositories'            , ['login' => 'ericsizemore']],
            ['{"Hello":"World"}', 'repository_contributions', ['login' => 'ericsizemore', 'page' => 1, 'per_page' => 30]],
            ['{"Hello":"World"}', 'subscriptions'           , []]
        ];
    }

    /**
     * Test the user endpoint
     *
     * @param string $expected
     * @param string $endpoint
     * @param array<string, int|string> $options
     */
    #[DataProvider('dataUserProvider')] 
    public function testUser(string $expected, string $endpoint, array $options): void
    {
        $this->stub->client = $this->client;
        $response = $this->stub->user($endpoint, $options);
        self::assertInstanceOf(Response::class, $response);
        self::assertEquals($expected, $response->getBody()->getContents());
    }

    /**
     * Test the user endpoint with an invalid $endpoint arg specified.
     */
    public function testUserInvalidEndpoint(): void
    {
        $this->stub->client = $this->client;
        $this->expectException(\InvalidArgumentException::class);
        $response = $this->stub->user('notvalid', ['login' => 'ericsizemore']);
    }

    /**
     * Test the iser endpoint with a valid $endpoint arg and invalid $options specified.
     */
    public function testUserInvalidOptions(): void
    {
        $this->stub->client = $this->client;
        $this->expectException(\InvalidArgumentException::class);
        $response = $this->stub->user('packages', ['huh' => 'what']);
    }

    /**
     * Test the toRaw function. It should return the raw response json
     */
    public function testRaw(): void
    {
        $this->stub->client = $this->client;
        $response = $this->stub->user('dependencies', ['login' => 'ericsizemore']);
        self::assertInstanceOf(Response::class, $response);
        self::assertEquals('{"Hello":"World"}', $this->stub->raw($response));
    }

    /**
     * Test the toArray function. It decodes the raw json data into an associative array.
     */
    public function testToArray(): void
    {
        $this->stub->client = $this->client;
        $response = $this->stub->user('dependencies', ['login' => 'ericsizemore']);
        self::assertInstanceOf(Response::class, $response);
        self::assertEquals(['Hello' => 'World'], $this->stub->toArray($response));
    }

    /**
     * Test the toObject function. It decodes the raw json data and creates a \stdClass object.
     */
    public function testToObject(): void
    {
        $this->stub->client = $this->client;
        $response = $this->stub->user('dependencies', ['login' => 'ericsizemore']);
        self::assertInstanceOf(Response::class, $response);

        $expected = new \stdClass;
        $expected->Hello = 'World';
        self::assertEquals($expected, $this->stub->toObject($response));
    }
}
