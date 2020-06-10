<?php

namespace Lemon\Http\Client;

use Lemon\Http\Client\Handler\ClientHandler;
use Lemon\Http\Client\Middleware\MiddlewareChain;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The HTTP client with middleware client
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class MiddlewareAwareClient implements ClientInterface
{
    /**
     * @var \Psr\Http\Client\ClientInterface
     */
    private $client;

    /**
     * @var array
     */
    private $middlewareChain;

    /**
     * Constructor
     *
     * @param  \Psr\Http\Client\ClientInterface $client
     * @param  array $middlewareChain
     */
    public function __construct(ClientInterface $client, array $middlewareChain = [])
    {
        $this->client = $client;
        $this->middlewareChain = $middlewareChain;
    }

    /**
     * Forward to client
     *
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return \call_user_func([$this->client, $name], ...$arguments);
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
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param  \Psr\Http\MessageRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $handler = new ClientHandler($this->client);
        $middleware = new MiddlewareChain($this->middlewareChain);

        return $middleware->process($request, $handler);
    }
}
