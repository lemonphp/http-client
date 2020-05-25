<?php

namespace Lemon\Http\Client\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;
use LogicException;

/**
 * The AddPathPrefix middleware
 *
 * Add path prefix to request URI
 *
 * @package     Lemon\Http\Client\Middleware
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
final class AddPathPrefix implements MiddlewareInterface
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * Force replace
     *
     * @var bool
     */
    private $force = false;

    /**
     * @param string $prefix
     */
    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;

        if (empty($this->path)) {
            throw new LogicException('Path can not empty');
        }
    }

    /**
     * Adds a prefix in the beginning of the URL's path.
     *
     * The prefix is not added if that prefix is already on the URL's path (exclude `force = true`).
     * This will fail on the edge case of the prefix being repeated, for example if `https://example.com/api/api/foo`
     * is a valid URL on the server and the configured prefix is `/api`.
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  \Lemon\Http\Client\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        // Add path prefix
        if ($this->force || substr($path, 0, strlen($this->prefix)) !== $this->prepix) {
            $request = $request->withUri($request->getUri()->withPath($this->prefix . $path));
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
