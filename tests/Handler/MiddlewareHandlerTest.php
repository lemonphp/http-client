<?php

namespace Tests\Handler;

use Lemon\Http\Client\Handler\MiddlewareHandler;
use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

/**
 * The middleware handler test
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class MiddlewareHandlerTest extends TestCase
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
     * Test it should call middleware->process() once
     *
     * @return void
     */
    public function testItShouldCallMiddlewareProcess()
    {
        $request = $this->requestFactory->createRequest('GET', 'https://example.com');
        $response = $this->responseFactory->createResponse(200);
        $nextHandler = $this->getMockForAbstractClass(RequestHandlerInterface::class);

        $middleware = $this->getMockForAbstractClass(MiddlewareInterface::class);
        $middleware->expects($this->once())
            ->method('process')
            ->with($request, $nextHandler)
            ->willReturn($response);

        $testHandler = new MiddlewareHandler($middleware, $nextHandler);

        $this->assertSame($response, $testHandler->handle($request));
    }
}
