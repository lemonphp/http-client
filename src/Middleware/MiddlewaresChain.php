<?php

namespace Lemon\Http\Client\Middleware;

use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MiddlewaresChain
{
    /**
     * Middeware list
     *
     * @var \Lemon\Http\Client\MiddlewareInterface[]
     */
    protected $middlewares;

    /**
     * Constructor
     *
     * @param array $middlewares
     */
    public function __construct(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * Add middleware
     *
     * @param  \Lemon\Http\Client\MiddlewareInterface $middleware
     * @return void
     */
    public function add(MiddlewareInterface $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  \Lemon\Http\Client\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        foreach ($this->middlewares as $middleware) {
            $handler = $this->wrapMiddleware($middleware, $handler);
        }

        return $handler->handle($request);
    }

    /**
     * Wrapping middleware to request handler interface
     *
     * @param  \Lemon\Http\Client\MiddlewareInterface $middleware
     * @param  \Lemon\Http\Client\RequestHandlerInterface $nextHandler
     * @return \Lemon\Http\Client\RequestHandlerInterface
     */
    protected function wrapMiddleware(
        MiddlewareInterface $middleware,
        RequestHandlerInterface $nextHandler
    ): RequestHandlerInterface {
        return new class ($middleware, $nextHandler) implements RequestHandlerInterface
        {
            /**
             * @var \Lemon\Http\Client\MiddlewareInterface
             */
            private $middleware;

            /**
             * @var \Lemon\Http\Client\RequestHandlerInterface
             */
            private $nextHandler;

            /**
             * @param \Lemon\Http\Client\MiddlewareInterface $middleware
             * @param \Lemon\Http\Client\RequestHandlerInterface $nextHandler
             */
            public function __construct(MiddlewareInterface $middleware, RequestHandlerInterface $nextHandler)
            {
                $this->middleware = $middleware;
                $this->nextHandler = $nextHandler;
            }

            /**
             * @param  \Psr\Http\Message\RequestInterface $request
             * @return \Psr\Http\Message\ResponseInterface
             */
            public function handle(RequestInterface $request): ResponseInterface
            {
                return $this->middleware->process($request, $this->nextHandler);
            }
        };
    }
}
