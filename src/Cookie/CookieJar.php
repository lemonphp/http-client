<?php

namespace Lemon\Http\Client\Cookie;

use Countable;
use Iterator;
use IteratorAggregate;
use SplObjectStorage;

/**
 * Cookie Jar holds a set of Cookies.
 */
final class CookieJar implements Countable, IteratorAggregate
{
    /**
     * @var \SplObjectStorage
     */
    private $cookies;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cookies = new SplObjectStorage();
    }

    /**
     * Checks if there is a cookie.
     *
     * @param  Cookie $cookie
     * @return bool
     */
    public function hasCookie(Cookie $cookie): bool
    {
        return $this->cookies->contains($cookie);
    }

    /**
     * Adds a cookie.
     *
     * @param  Cookie $cookie
     * @return void
     */
    public function addCookie(Cookie $cookie): void
    {
        if (!$this->hasCookie($cookie)) {
            $cookies = $this->getMatchedCookies($cookie);

            foreach ($cookies as $matchingCookie) {
                if (
                    $cookie->getValue() !== $matchingCookie->getValue() ||
                    $cookie->getMaxAge() > $matchingCookie->getMaxAge()
                ) {
                    $this->removeCookie($matchingCookie);
                    continue;
                }
            }

            if ($cookie->hasValue()) {
                $this->cookies->attach($cookie);
            }
        }
    }

    /**
     * Removes a cookie.
     *
     * @param  Cookie $cookie
     * @return void
     */
    public function removeCookie(Cookie $cookie): void
    {
        $this->cookies->detach($cookie);
    }

    /**
     * Returns the cookies.
     *
     * @return \Iterator<Cookie>
     */
    public function getCookies(): Iterator
    {
        $match = function ($matchCookie) {
            return true;
        };

        return $this->findCookies($match);
    }

    /**
     * Returns all matched cookies.
     *
     * @return \Iterator<Cookie>
     */
    public function getMatchedCookies(Cookie $cookie): Iterator
    {
        $match = function ($matchCookie) use ($cookie) {
            return $matchCookie->match($cookie);
        };

        return $this->findCookies($match);
    }

    /**
     * Checks if there are cookies.
     *
     * @return bool
     */
    public function hasCookies(): bool
    {
        return $this->cookies->count() > 0;
    }

    /**
     * Sets the cookies and removes any previous one.
     *
     * @param  array|Cookie[] $cookies
     * @return void
     */
    public function setCookies(array $cookies): void
    {
        $this->clear();
        $this->addCookies($cookies);
    }

    /**
     * Adds some cookies.
     *
     * @param  array|Cookie[] $cookies
     * @return void
     */
    public function addCookies(array $cookies): void
    {
        foreach ($cookies as $cookie) {
            $this->addCookie($cookie);
        }
    }

    /**
     * Removes some cookies.
     *
     * @param \Iterator<Cookie> $cookies
     * @return void
     */
    public function removeCookies(Iterator $cookies): void
    {
        foreach ($cookies as $cookie) {
            $this->removeCookie($cookie);
        }
    }

    /**
     * Removes cookies which match the given parameters.
     *
     * NULL means that parameter should not be matched
     *
     * @param  string|null $name
     * @param  string|null $domain
     * @param  string|null $path
     * @return void
     */
    public function removeMatchedCookies(?string $name = null, ?string $domain = null, ?string $path = null): void
    {
        $match = function ($matchCookie) use ($name, $domain, $path) {
            $match = true;

            if ($name !== null) {
                $match = $match && ($matchCookie->getName() === $name);
            }

            if ($domain !== null) {
                $match = $match && $matchCookie->matchDomain($domain);
            }

            if ($path !== null) {
                $match = $match && $matchCookie->matchPath($path);
            }

            return $match;
        };

        $cookies = $this->findCookies($match);

        $this->removeCookies($cookies);
    }

    /**
     * Remove expired cookies
     *
     * @return void
     */
    public function removeExpiredCookies(): void
    {
        $math = function ($matchCookie) {
            return $matchCookie->isExpired();
        };

        $cookies = $this->findCookies($math);

        $this->removeCookies($cookies);
    }

    /**
     * Removes all cookies.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->cookies = new SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->cookies->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return clone $this->cookies;
    }

    /**
     * Finds cookies based on a matching function.
     *
     * @param  callable $match The matching function
     * @return \Iterator<Cookie>
     */
    private function findCookies(callable $match): Iterator
    {
        foreach ($this->cookies as $cookie) {
            if ($match($cookie)) {
                yield $cookie;
            }
        }
    }
}
