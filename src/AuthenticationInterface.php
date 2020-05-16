<?php

namespace Lemon\Http\Client;

use Psr\Http\Message\RequestInterface;

interface AuthenticationInterface
{
    public function authenticate(RequestInterface $request): RequestInterface;
}
