<?php

namespace Lemon\Http\Client;

use Psr\Http\Message\ResponseInterface;

/**
 * The HTTP methods client interface
 *
 * Convenience HTTP client that integrates the MessageFactory in order to send
 * requests in the following form:.
 *
 * $client->get('/foo');
 *
 * The client also exposes the sendRequest methods of the wrapped HttpClient.
 *
 * @package     Lemon\Http\Client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
interface HttpMethodsClientInterface
{
    /**
     * Sends a GET request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function get($uri, array $headers = []): ResponseInterface;

    /**
     * Sends an HEAD request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function head($uri, array $headers = []): ResponseInterface;

    /**
     * Sends a TRACE request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function trace($uri, array $headers = []): ResponseInterface;

    /**
     * Sends a POST request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @param  \Psr\Http\Message\StreamInterface|string|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function post($uri, array $headers = [], $body = null): ResponseInterface;

    /**
     * Sends a PUT request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @param  \Psr\Http\Message\StreamInterface|string|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function put($uri, array $headers = [], $body = null): ResponseInterface;

    /**
     * Sends a PATCH request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @param  \Psr\Http\Message\StreamInterface|string|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function patch($uri, array $headers = [], $body = null): ResponseInterface;

    /**
     * Sends a DELETE request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @param  \Psr\Http\Message\StreamInterface|string|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function delete($uri, array $headers = [], $body = null): ResponseInterface;

    /**
     * Sends an OPTIONS request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @param  \Psr\Http\Message\StreamInterface|string|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function options($uri, array $headers = [], $body = null): ResponseInterface;

    /**
     * Sends a request with any HTTP method.
     *
     * @param  string $method The HTTP method to use
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @param  \Psr\Http\Message\StreamInterface|string|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function send(string $method, $uri, array $headers = [], $body = null): ResponseInterface;
}
