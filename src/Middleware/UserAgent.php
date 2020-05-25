<?php

namespace Lemon\Http\Client\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;

/**
 * The UserAgent middleware
 *
 * Add or replace UserAgent header
 *
 * @package     Lemon\Http\Client\Middleware
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
final class UserAgent implements MiddlewareInterface
{
    /**
     * @var string
     */
    private $userAgent;

    /**
     * Force replace
     *
     * @var bool
     */
    private $force = false;

    /**
     * @param string $userAgent
     */
    public function __construct(string $userAgent = null)
    {
        $this->userAgent = $userAgent ?: sprintf('HTTPClient PHP/%s', PHP_VERSION);
    }

    /**
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  \Lemon\Http\Client\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->force || !$request->hasHeader('User-Agent')) {
            $request = $request->withHeader('User-Agent', $this->userAgent);
        }

        return $handler->handle($request);
    }

    /**
     * Set force replace flag value
     *
     * @param  bool $force
     * @return self
     */
    public function force(bool $value = true): self
    {
        $this->force = $value;

        return $this;
    }
}
