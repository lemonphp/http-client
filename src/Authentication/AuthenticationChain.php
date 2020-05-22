<?php

namespace Lemon\Http\Client\Authentication;

use InvalidArgumentException;
use Lemon\Http\Client\AuthenticationInterface;
use Psr\Http\Message\RequestInterface;

/**
 * The authentication chain class
 *
 * Authenticate request with given authentication chain
 *
 * @package     Lemon\Http\Client\Authentication
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
final class AuthenticationChain implements AuthenticationInterface
{
    /**
     * @var Lemon\Http\Client\AuthenticationInterface[]
     */
    private $authenticationChain;

    /**
     * @param  array $authenticationChain
     * @throws \InvalidArgumentException
     */
    public function __construct(array $authenticationChain)
    {
        foreach ($authenticationChain as $authentication) {
            if (!$authentication instanceof AuthenticationInterface) {
                throw new InvalidArgumentException(
                    'Members of the authentication chain must be of type ' . AuthenticationInterface::class
                );
            }
        }

        $this->authenticationChain = $authenticationChain;
    }

    /**
     * Authenticate
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\RequestInterface
     */
    public function authenticate(RequestInterface $request): RequestInterface
    {
        foreach ($this->authenticationChain as $authentication) {
            $request = $authentication->authenticate($request);
        }

        return $request;
    }
}
