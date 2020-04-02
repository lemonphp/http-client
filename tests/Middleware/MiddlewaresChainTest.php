<?php

namespace Tests\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Lemon\Http\Client\Middleware\MiddlewaresChain;
use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

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
     * Testing logs
     *
     * @var array
     */
    public static $logs = [];

    /**
     * Test middleware shoule process
     *
     * @return void
     */
    public function testItShouldProcessMiddlewaresChain()
    {
        // Prepare data for test
        $one = $this->makeMiddeware('one');
        $two = $this->makeMiddeware('two');
        $three = $this->makeMiddeware('three');
        $four = $this->makeMiddeware('four');
        $request = (new RequestFactory())->createRequest('GET', 'https://example.com');
        $response = (new ResponseFactory())->createResponse(StatusCodeInterface::STATUS_OK);
        $handler = $this->getMockForAbstractClass(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $chain = new MiddlewaresChain([$one, $two, $three, $four]);
        self::$logs = [];

        // Aserts
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
        ], self::$logs);
    }

    /**
     * Make middeware for test
     *
     * @param  string $prefix
     * @return \Lemon\Http\Client\MiddlewareInterface
     */
    protected function makeMiddeware(string $prefix): MiddlewareInterface
    {
        return new class ($prefix) implements MiddlewareInterface
        {
            /**
             * @var string
             */
            protected $prefix;

            /**
             * @param string $prefix
             */
            public function __construct(string $prefix)
            {
                $this->prefix = $prefix;
            }

            /**
             * @param  \Psr\Http\Message\RequestInterface $request
             * @param  \Lemon\Http\Client\RequestHandlerInterface $handler
             * @return \Psr\Http\Message\ResponseInterface
             */
            public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                MiddlewaresChainTest::$logs[] = "{$this->prefix}-pre";

                $response = $handler->handle($request);

                MiddlewaresChainTest::$logs[] = "{$this->prefix}-post";

                return $response;
            }
        };
    }
}
