<?php

namespace Lemon\Http\Client;

use Lemon\Http\Client\Handler\ClientHandler;
use Lemon\Http\Client\Handler\MiddlewareHandler;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The HTTP client with middlewares client
 *
 * @package     Lemon\Http\Client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class MiddlewaresAwareClient implements ClientInterface
{
    /**
     * @var \Psr\Http\Client\ClientInterface
     */
    private $client;

    /**
     * @var array
     */
    private $middlewares;

    /**
     * Constructor
     *
     * @param  \Psr\Http\Client\ClientInterface $client
     * @param  array $middlewares
     */
    public function __construct(ClientInterface $client, array $middlewares = [])
    {
        $this->client = $client;
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
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param  \Psr\Http\MessageRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $handler = new ClientHandler($this->client);

        foreach($this->middlewares as $middleware) {
            $handler = new MiddlewareHandler($middleware, $handler);
        }

        return $handler->handle($request);
    }
}
