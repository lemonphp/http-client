<?php

namespace Lemon\Http\Client;

use Lemon\Http\Client\Handler\TransportHandler;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The simple HTTP client
 *
 * @package     Lemon\Http\Client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class Client implements ClientInterface
{
    /**
     * Transport handler
     *
     * @var \Lemon\Http\Client\TransportInterface
     */
    protected $transport;

    /**
     * Client constructor
     *
     * @param  \Lemon\Http\Client\TransportInterface  $transport
     */
    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
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
        return $this->transport->send($request);
    }
}
