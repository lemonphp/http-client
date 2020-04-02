<?php

namespace Lemon\Http\Client\Handler;

use Lemon\Http\Client\RequestHandlerInterface;
use Lemon\Http\Client\ClientOptions;
use Lemon\Http\Client\TransportInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The request handler wrapper a transport
 *
 * @package     Lemon\Http\Client\Handler
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class TransportHandler implements RequestHandlerInterface
{
    /**
     * @var \Lemon\Http\Client\TransportInterface
     */
    private $transport;

    /**
     * @var \Lemon\Http\Client\ClientOptions
     */
    private $options;

    /**
     * Constructor
     *
     * @param  \Lemon\Http\Client\TransportInterface $transport
     * @param  \Lemon\Http\Client\ClientOptions $options
     */
    public function __construct(TransportInterface $transport, ClientOptions $options)
    {
        $this->transport = $transport;
        $this->options = $options;
    }

    /**
     * @param  \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        return $this->transport->send($request, $this->options);
    }
}
