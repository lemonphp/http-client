<?php

namespace Tests\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Lemon\Http\Client\Middleware\MiddlewaresChain;
use Lemon\Http\Client\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Tests\TestMiddleware;

/**
 * The middlewares chain test
 *
 * @package     Tests\Middleware
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class MiddlewaresChainTest extends TestCase
{
    /**
     * Test middleware shoule process
     *
     * @return void
     */
    public function testItShouldProcessMiddlewaresChain()
    {
        // Prepare data for test
        $request = (new RequestFactory())->createRequest('GET', 'https://example.com');
        $response = (new ResponseFactory())->createResponse(StatusCodeInterface::STATUS_OK);
        $handler = $this->getMockForAbstractClass(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $middlewares = [
            new TestMiddleware('one'),
            new TestMiddleware('two'),
            new TestMiddleware('three'),
            new TestMiddleware('four'),
        ];
        $chain = new MiddlewaresChain($middlewares);

        // empty logs
        TestMiddleware::$logs = [];

        static::assertSame($response, $chain->process($request, $handler));
        static::assertSame([
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
}
