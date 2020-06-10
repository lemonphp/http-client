<?php

namespace Lemon\Http\Client\Middleware;

use InvalidArgumentException;
use Lemon\Http\Client\Handler\MiddlewareHandler;
use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The chain middleware
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
final class MiddlewareChain implements MiddlewareInterface
{
    /**
     * Middeware list
     *
     * @var \Lemon\Http\Client\MiddlewareInterface[]
     */
    protected $middlewareChain;

    /**
     * Constructor
     *
     * @param array $middlewareChain
     * @throws \InvalidArgumentException
     */
    public function __construct(array $middlewareChain)
    {
        foreach ($middlewareChain as $middleware) {
            if (!$middleware instanceof MiddlewareInterface) {
                throw new InvalidArgumentException(
                    'Members of the authentication chain must be of type ' . MiddlewareInterface::class
                );
            }
        }

        $this->middlewareChain = $middlewareChain;
    }

    /**
     * Add middleware
     *
     * @param  \Lemon\Http\Client\MiddlewareInterface $middleware
     * @return void
     */
    public function add(MiddlewareInterface $middleware)
    {
        $this->middlewareChain[] = $middleware;
    }

    /**
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  \Lemon\Http\Client\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        foreach ($this->middlewareChain as $middleware) {
            $handler = new MiddlewareHandler($middleware, $handler);
        }

        return $handler->handle($request);
    }
}
