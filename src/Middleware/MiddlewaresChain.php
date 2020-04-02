<?php

namespace Lemon\Http\Client\Middleware;

use Lemon\Http\Client\Handler\MiddlewareHandler;
use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The HTTP client
 *
 * @package     Lemon\Http\Client\Middleware
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class MiddlewaresChain implements MiddlewareInterface
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
            $handler = new MiddlewareHandler($middleware, $handler);
        }

        return $handler->handle($request);
    }
}
