<?php

namespace Lemon\Http\Client\Authentication;

use Lemon\Http\Client\AuthenticationInterface;
use Psr\Http\Message\RequestInterface;

/**
 * The auto basic authentication class
 *
 * Authenticate request with user info from request's URI
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
final class AutoBasicAuth implements AuthenticationInterface
{
    /**
     * Whether user info should be removed from the URI.
     *
     * @var bool
     */
    private $shouldRemoveUserInfo;

    /**
     * @param bool $shouldRemoveUserInfo
     */
    public function __construct(bool $shouldRemoveUserInfo = true)
    {
        $this->shouldRemoveUserInfo = (bool) $shouldRemoveUserInfo;
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

            $authorizationHeader = \sprintf('Basic %s', \base64_encode($userInfo));

            return $request->withHeader('Authorization', $authorizationHeader);
        }

        return $request;
    }
}
