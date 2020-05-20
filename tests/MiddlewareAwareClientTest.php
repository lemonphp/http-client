<?php

namespace Tests;

use Lemon\Http\Client\MiddlewareAwareClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

/**
 * The MiddlewareAwareClient test
 *
 * @package     Tests
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class MiddlewareAwareClientTest extends TestCase
{
    use NonPublicAccessible;

    /**
     * Test client should send request
     *
     * @return void
     */
    public function testItShouldSendRequest()
    {
        $request = (new RequestFactory())->createRequest('GET', 'https://example.com');
        $response = (new ResponseFactory())->createResponse(200);

        $client = $this->getMockForAbstractClass(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $middlewares = [
            new TestMiddleware('one'),
            new TestMiddleware('two'),
            new TestMiddleware('three'),
            new TestMiddleware('four'),
        ];

        $testClient = new MiddlewareAwareClient($client, $middlewares);

        // empty logs
        TestMiddleware::$logs = [];

        $this->assertSame($response, $testClient->sendRequest($request));
        $this->assertSame([
            'four-pre',
            'three-pre',
            'two-pre',
            'one-pre',
            'one-post',
            'two-post',
            'three-post',
            'four-post',
        ], TestMiddleware::$logs);
    }

    /**
     * Test it should add middleware to end of middleware list
     *
     * @return void
     */
    public function testItShouldAddMiddlewareToList()
    {
        $client = $this->getMockForAbstractClass(ClientInterface::class);
        $middlewares = [
            new TestMiddleware('one'),
            new TestMiddleware('two'),
            new TestMiddleware('three'),
            new TestMiddleware('four'),
        ];

        $testClient = new MiddlewareAwareClient($client, $middlewares);

        // Assert before add middleware
        $this->assertSame($middlewares, $this->getNonPublicProperty($testClient, 'middlewares'));

        $testClient->add($five = new TestMiddleware('five'));
        $middlewares = $this->getNonPublicProperty($testClient, 'middlewares');

        // Assert after add new middleware
        $this->assertSame($five, end($middlewares));
    }
}
