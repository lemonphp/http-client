<?php

namespace Lemon\Http\Client\Transport;

use Lemon\Http\Client\TransportInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The Mockup transport
 *
 * It alway return fixed response.
 * It is very helpful for testing.
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
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
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        return $this->response;
    }
}
