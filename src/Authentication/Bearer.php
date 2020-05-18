<?php

namespace Lemon\Http\Client\Authentication;

use Lemon\Http\Client\AuthenticationInterface;
use Psr\Http\Message\RequestInterface;

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
        $authorizationHeader = sprintf('Bearer %s', $this->token);

        return $request->withHeader('Authorization', $authorizationHeader);
    }
}
