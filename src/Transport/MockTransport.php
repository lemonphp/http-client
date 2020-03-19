<?php

namespace Lemon\Http\Client\Transport;

use Lemon\Http\Client\TransportInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Mockup transport
 *
 * It alway return fixed response
 */
class MockTransport implements TransportInterface
{
    /**
     * Constructor
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        return $this->response;
    }
}
