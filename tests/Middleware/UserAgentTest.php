<?php

namespace Tests\Middleware;

use Lemon\Http\Client\Middleware\UserAgent;
use Lemon\Http\Client\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

/**
 * The UserAgent middleware test
 *
 * @package     Tests\Middleware
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class UserAgentTest extends TestCase
{
    /**
     * Test it shoule set request's UserAgent if it hadn't yet
     *
     * @return void
     */
    public function testItShouldSetRequestUserAgentIfItHadNot()
    {
        $userAgent = 'Test UserAgent';

        $request = (new RequestFactory())->createRequest('GET', 'https://example.com');
        $response = (new ResponseFactory())->createResponse(200);
        $handler = $this->getMockForAbstractClass(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->withAnyParameters()
            ->willReturnCallback(function (RequestInterface $request) use ($response, $userAgent) {
                static::assertSame($userAgent, $request->getHeaderLine('User-Agent'));
                return $response;
            });

        $middleware = new UserAgent($userAgent);
        static::assertEmpty($request->getHeaderLine('User-Agent'));

        $middleware->process($request, $handler);
    }
}
