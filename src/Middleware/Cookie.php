<?php

namespace Lemon\Http\Client\Middleware;

use Lemon\Http\Client\Cookie\Cookie as CookieRecord;
use Lemon\Http\Client\Cookie\CookieJar;
use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Cookie implements MiddlewareInterface
{
    /**
     * @var \Lemon\Http\Client\Cookie\CookieJar
     */
    private $cookieJar;

    /**
     * Constructor
     *
     * @param \Lemon\Http\Client\Cookie\CookieJar $cookieJar
     */
    public function __construct(CookieJar $cookieJar)
    {
        $this->cookieJar = $cookieJar;
    }

    /**
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  \Lemon\Http\Client\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cookies = [];
        foreach ($this->cookieJar->getCookies() as $cookie) {
            // Ignore and remove expired cookie
            if ($cookie->isExpired()) {
                $this->cookieJar->removeCookie($cookie);
                continue;
            }

            if (!$cookie->matchDomain($request->getUri()->getHost())) {
                continue;
            }

            if (!$cookie->matchPath($request->getUri()->getPath())) {
                continue;
            }

            if ($cookie->isSecure() && ('https' !== $request->getUri()->getScheme())) {
                continue;
            }

            $cookies[] = \sprintf('%s=%s', $cookie->getName(), $cookie->getValue());
        }

        if (!empty($cookies)) {
            $request = $request->withAddedHeader('Cookie', \implode('; ', \array_unique($cookies)));
        }

        // Handler request
        $response = $handler->handle($request);

        // Update cookies from response
        if ($response->hasHeader('Set-Cookie')) {
            $setCookies = $response->getHeader('Set-Cookie');
            $domain = $request->getUri()->getHost();

            foreach ($setCookies as $setCookie) {
                $cookie = CookieRecord::parse(
                    $setCookie,
                    $domain,
                    $request->getUri()->getPath()
                );

                // Cookie invalid do not use it
                if (null === $cookie) {
                    continue;
                }

                // Restrict setting cookie from another domain
                if (!preg_match("/\.{$cookie->getDomain()}$/", '.' . $domain)) {
                    continue;
                }

                $this->cookieJar->addCookie($cookie);
            }
        }

        return $response;
    }
}
