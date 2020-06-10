<?php

namespace Lemon\Http\Client\Middleware;

use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * The SetBaseUri middleware
 *
 * Set base host and path for request URI
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class SetBaseUri implements MiddlewareInterface
{
    /**
     * @var \Psr\Http\Message\UriInterface
     */
    private $baseUri;

    /**
     * Constructor
     *
     * @param  \Psr\Http\Message\UriInterface $baseUri
     */
    public function __construct(UriInterface $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    /**
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  \Lemon\Http\Client\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();

        if ($this->baseUri->getHost() !== '') {
            $uri = $uri->withScheme($this->baseUri->getScheme())
                ->withHost($this->baseUri->getHost())
                ->withPort($this->baseUri->getPort());
        }

        if ($this->baseUri->getPath() !== '') {
            $uri = $uri->withPath($this->baseUri->getPath() . '/' . $uri->getPath());
        }

        return $handler->handle($request->withUri($uri));
    }
}
