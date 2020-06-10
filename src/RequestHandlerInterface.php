<?php

namespace Lemon\Http\Client;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The request handler interface
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
interface RequestHandlerInterface
{
    /**
     * @param  \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(RequestInterface $request): ResponseInterface;
}
