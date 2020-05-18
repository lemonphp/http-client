<?php

namespace Lemon\Http\Client\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;

final class SetHeaders implements MiddlewareInterface
{
    /**
     * @var string[]
     */
    private $headers;

    /**
     * Force replace
     *
     * @var bool
     */
    private $force = false;

    /**
     * @param string[] $headers
     */
    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  \Lemon\Http\Client\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Set headers
        foreach ($this->headers as $header => $value) {
            if ($this->force || !$request->hasHeader($header)) {
                $request = $request->withHeader($header, $value);
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
