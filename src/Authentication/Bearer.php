<?php

namespace Lemon\Http\Client\Authentication;

use Lemon\Http\Client\AuthenticationInterface;
use Psr\Http\Message\RequestInterface;

/**
 * The Bearer authentication class
 *
 * Authenticate request with given bearer token
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
final class Bearer implements AuthenticationInterface
{
    /**
     * @var string
     */
    private $token;

    /**
     * @param  string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Authenticate
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\RequestInterface
     */
    public function authenticate(RequestInterface $request): RequestInterface
    {
        $authorizationHeader = \sprintf('Bearer %s', $this->token);

        return $request->withHeader('Authorization', $authorizationHeader);
    }
}
