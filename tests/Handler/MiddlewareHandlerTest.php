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
 * @package     Tests\Handler
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class MiddlewareHandlerTest extends TestCase
{
    /**
     * Test it should call middleware->process() once
     *
     * @return void
     */
    public function testItShouldCallMiddlewareProcess()
    {
        $request = (new RequestFactory())->createRequest('GET', 'https://example.com');
        $response = (new ResponseFactory())->createResponse(200);
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
