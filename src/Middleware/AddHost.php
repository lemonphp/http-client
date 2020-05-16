<?php

namespace Lemon\Http\Client\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;
use LogicException;

final class AddHost implements MiddlewareInterface
{
    /**
     * @var string
     */
    private $host;

    /**
     * Force replace
     *
     * @var bool
     */
    private $force = false;

    /**
     * @param string $host
     */
    public function __construct(string $host)
    {
        $this->host = $host;

        if (empty($this->host)) {
            throw new LogicException('Host can not empty');
        }
    }

    /**
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  \Lemon\Http\Client\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Add host
        if ($this->force || $request->getUri()->getHost() === '') {
            $request = $request->withUri($request->getUri()->withHost($this->host));
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
