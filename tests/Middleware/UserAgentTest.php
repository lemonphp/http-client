<?php

namespace Tests\Middleware;

use Lemon\Http\Client\Middleware\UserAgent;
use Lemon\Http\Client\RequestHandlerInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

/**
 * The UserAgent middleware test
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class UserAgentTest extends TestCase
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
     * Test it shoule set request's UserAgent if it hadn't yet
     *
     * @return void
     */
    public function testItShouldSetRequestUserAgentIfItHadNot()
    {
        $userAgent = 'Test UserAgent';

        $request = $this->requestFactory->createRequest('GET', 'https://example.com');
        $response = $this->responseFactory->createResponse(200);
        $handler = $this->getMockForAbstractClass(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->withAnyParameters()
            ->willReturnCallback(function (RequestInterface $request) use ($response, $userAgent) {
                Assert::assertSame($userAgent, $request->getHeaderLine('User-Agent'));
                return $response;
            });

        $middleware = new UserAgent($userAgent);
        $this->assertEmpty($request->getHeaderLine('User-Agent'));

        $middleware->process($request, $handler);
    }
}
