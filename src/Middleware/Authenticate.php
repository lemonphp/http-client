<?php

namespace Lemon\Http\Client\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Lemon\Http\Client\AuthenticationInterface;
use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;

/**
 * The Authenticate middleware
 *
 * Authenticate request by add header
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
final class Authenticate implements MiddlewareInterface
{
    /**
     * @var \Lemon\Http\Client\AuthenticationInterface
     */
    private $authentication;

    /**
     * Constructor
     *
     * @param \Lemon\Http\Client\AuthenticationInterface $authentication
     */
    public function __construct(AuthenticationInterface $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  \Lemon\Http\Client\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $this->authentication->authenticate($request);

        return $handler->handle($request);
    }
}
