<?php

namespace Lemon\Http\Client\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;

/**
 * The RemoveHeaders middleware
 *
 * Remove some request headers
 *
 * @package     Lemon\Http\Client\Middleware
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
final class RemoveHeaders implements MiddlewareInterface
{
    /**
     * @var string[]
     */
    private $headers;

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
        // Clear headers
        foreach ($this->headers as $header) {
            if ($request->hasHeader($header)) {
                $request = $request->withoutHeader($header);
            }
        }

        return $handler->handle($request);
    }
}
