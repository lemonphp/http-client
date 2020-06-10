<?php

namespace Lemon\Http\Client\Authentication;

use Lemon\Http\Client\AuthenticationInterface;
use Psr\Http\Message\RequestInterface;

/**
 * The WSSE authentication class
 *
 * Authenticate request with given username and password with WSSE spec
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
final class Wsse implements AuthenticationInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @param  string $username
     * @param  string $password
     */
    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Authenticate
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\RequestInterface
     */
    public function authenticate(RequestInterface $request): RequestInterface
    {
        $nonce = $this->generateNonce();
        $created = \date('c');

        $wsse = \sprintf(
            'UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"',
            $this->username,
            \base64_encode(\sha1($nonce . $created . $this->password, true)),
            \base64_encode($nonce),
            $created
        );

        return $request
            ->withHeader('Authorization', 'WSSE profile="UsernameToken"')
            ->withHeader('X-WSSE', $wsse)
        ;
    }

    /**
     * Generate nonce
     *
     * @see https://stackoverflow.com/questions/18910814/.../31419246#31419246
     *
     * @return string
     */
    private function generateNonce(): string
    {
        return \bin2hex(\random_bytes(16));
    }
}
