<?php

namespace Lemon\Http\Client\Handler;

use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The request handler wrapper a middleware
 *
 * @package     Lemon\Http\Client\Handler
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class MiddlewareHandler implements RequestHandlerInterface
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
     * Constructor
     *
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
}
