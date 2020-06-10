<?php

namespace Lemon\Http\Client;

use Psr\Http\Message\RequestInterface;

/**
 * The authentication interface
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
interface AuthenticationInterface
{
    /**
     * Authenticate request
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\RequestInterface
     */
    public function authenticate(RequestInterface $request): RequestInterface;
}
