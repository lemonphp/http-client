<?php

namespace Lemon\Http\Client\Middleware;

use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ContentLength implements MiddlewareInterface
{
    /**
     * Force replace
     *
     * @var bool
     */
    private $force = false;

    /**
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  \Lemon\Http\Client\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->force || !$request->hasHeader('Content-Length')) {
            $stream = $request->getBody();

            if (null === $stream->getSize()) {
                // TODO: Cannot determine the size so we use a chunk stream
            } else {
                $request = $request->withHeader('Content-Length', (string) $stream->getSize());
            }
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
