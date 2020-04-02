<?php

namespace Lemon\Http\Client\Handler;

use Lemon\Http\Client\RequestHandlerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The request handler wrapper a client
 *
 * @package     Lemon\Http\Client\Handler
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class ClientHandler implements RequestHandlerInterface
{
    /**
     * @var \Psr\Http\Client\ClientInterface
     */
    private $client;

    /**
     * Constructor
     *
     * @param  \Psr\Http\Client\ClientInterface
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param  \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }
}
