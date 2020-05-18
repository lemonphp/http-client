<?php

namespace Lemon\Http\Client\Authentication;

use Lemon\Http\Client\AuthenticationInterface;
use Psr\Http\Message\RequestInterface;

final class AuthBasicAuth implements AuthenticationInterface
{
    /**
     * Whether user info should be removed from the URI.
     *
     * @var bool
     */
    private $shouldRemoveUserInfo;

    /**
     * @param  bool $shouldRremoveUserInfo
     */
    public function __construct($shouldRremoveUserInfo = true)
    {
        $this->shouldRemoveUserInfo = (bool) $shouldRremoveUserInfo;
    }

    /**
     * Authenticate
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\RequestInterface
     */
    public function authenticate(RequestInterface $request): RequestInterface
    {
        $uri = $request->getUri();
        $userInfo = $uri->getUserInfo();

        if (!empty($userInfo)) {
            if ($this->shouldRemoveUserInfo) {
                $request = $request->withUri($uri->withUserInfo(''));
            }

            $request = $request->withHeader('Authorization', sprintf('Basic %s', base64_encode($userInfo)));
        }

        return $request;
    }
}
