<?php

namespace Tests\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Lemon\Http\Client\Middleware\MiddlewareChain;
use Lemon\Http\Client\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Tests\TestMiddleware;

/**
 * The middlewares chain test
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class MiddlewareChainTest extends TestCase
{
    /**
     * @var \Psr\Http\Message\RequestFactoryInterface
     */
    protected $requestFactory;

    /**
     * @var \Psr\Http\Message\ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->requestFactory = new RequestFactory();
        $this->responseFactory = new ResponseFactory();
    }

    /**
     * Test middleware shoule process
     *
     * @return void
     */
    public function testItShouldProcessMiddlewaresChain()
    {
        // Prepare data for test
        $request = $this->requestFactory->createRequest('GET', 'https://example.com');
        $response = $this->responseFactory->createResponse(StatusCodeInterface::STATUS_OK);
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
        $chain = new MiddlewareChain($middlewares);

        // empty logs
        TestMiddleware::$logs = [];

        $this->assertSame($response, $chain->process($request, $handler));
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
}
