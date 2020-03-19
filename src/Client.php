<?php

namespace Lemon\Http\Client;

use Lemon\Http\Client\Middleware\MiddlewaresChain;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Client implements ClientInterface
{
    /**
     * Client options
     *
     * @var \Lemon\Http\Client\RequestOptions
     */
    protected $options;

    /**
     * Client transport
     *
     * @var \Lemon\Http\Client\TransportInterface
     */
    protected $transport;

    /**
     * Middleware list
     *
     * @var \Lemon\Http\Client\MiddlewareInterface[]
     */
    protected $middlewares;

    /**
     * Client constructor
     *
     * @param  \Lemon\Http\Client\TransportInterface  $transport
     * @param  \Lemon\Http\Client\RequestOptions|null $options
     */
    public function __construct(TransportInterface $transport, ?RequestOptions $options = null)
    {
        $this->transport = $transport;
        $this->options = $options;
        $this->middlewares = [];
    }

    /**
     * Set middleware list
     *
     * @param  array $middlewares
     * @return self
     */
    public function setMiddlewares(array $middlewares)
    {
        $this->middlewares = $middlewares;

        return $this;
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
        $transport = $this->transport;
        $handler = new class ($transport) implements RequestHandlerInterface
        {
            /** @var \Lemon\Http\Client\TransportInterface */
            private $transport;

            /**
             * @param \Lemon\Http\Client\TransportInterface $transport
             */
            public function __construct(TransportInterface $transport)
            {
                $this->transport = $transport;
            }

            /**
             * @param  \Psr\Http\Message\RequestInterface $request
             * @return \Psr\Http\Message\ResponseInterface
             */
            public function handle(RequestInterface $request): ResponseInterface
            {
                return $this->transport->send($request);
            }
        };

        return (new MiddlewaresChain($this->middlewares))->process($request, $handler);
    }
}
