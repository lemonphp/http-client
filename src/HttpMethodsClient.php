<?php

namespace Lemon\Http\Client;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class HttpMethodsClient implements ClientInterface, HttpMethodsClientInterface
{
    /**
     * @var \Psr\Http\Client\ClientInterface
     */
    private $client;

    /**
     * @var \Psr\Http\Message\RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var \Psr\Http\Message\StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * Constructor
     *
     * @param \Psr\Http\Client\ClientInterface          $client
     * @param \Psr\Http\Message\StreamFactoryInterface  $streamFactory
     * @param \Psr\Http\Message\RequestFactoryInterface $requestFactory
     */
    public function __construct(
        ClientInterface $client,
        StreamFactoryInterface $streamFactory,
        RequestFactoryInterface $requestFactory
    ) {
        $this->client = $client;
        $this->streamFactory = $streamFactory;
        $this->requestFactory = $requestFactory;
    }

    /**
     * Forward to client
     *
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return \call_user_func([$this->client, $name], ...$arguments);
    }

    /**
     * Sends a GET request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function get($uri, array $headers = []): ResponseInterface
    {
        return $this->send('GET', $uri, $headers);
    }

    /**
     * Sends an HEAD request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function head($uri, array $headers = []): ResponseInterface
    {
        return $this->send('HEAD', $uri, $headers);
    }

    /**
     * Sends a TRACE request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function trace($uri, array $headers = []): ResponseInterface
    {
        return $this->send('TRACE', $uri, $headers);
    }

    /**
     * Sends a POST request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @param  \Psr\Http\Message\StreamInterface|string|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function post($uri, array $headers = [], $body = null): ResponseInterface
    {
        return $this->send('POST', $uri, $headers, $body);
    }

    /**
     * Sends a PUT request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @param  \Psr\Http\Message\StreamInterface|string|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function put($uri, array $headers = [], $body = null): ResponseInterface
    {
        return $this->send('PUT', $uri, $headers, $body);
    }

    /**
     * Sends a PATCH request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @param  \Psr\Http\Message\StreamInterface|string|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function patch($uri, array $headers = [], $body = null): ResponseInterface
    {
        return $this->send('PATCH', $uri, $headers, $body);
    }

    /**
     * Sends a DELETE request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @param  \Psr\Http\Message\StreamInterface|string|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function delete($uri, array $headers = [], $body = null): ResponseInterface
    {
        return $this->send('DELETE', $uri, $headers, $body);
    }

    /**
     * Sends an OPTIONS request.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @param  \Psr\Http\Message\StreamInterface|string|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function options($uri, array $headers = [], $body = null): ResponseInterface
    {
        return $this->send('OPTIONS', $uri, $headers, $body);
    }

    /**
     * Sends a request with any HTTP method.
     *
     * @param  string $method The HTTP method to use
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @param  \Psr\Http\Message\StreamInterface|string|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function send(string $method, $uri, array $headers = [], $body = null): ResponseInterface
    {
        $request = $this->requestFactory->createRequest($method, $uri);

        // Headers
        foreach ($headers as $header => $value) {
            $request = $request->withAddedHeader($header, $value);
        }

        // Body
        if (\is_string($body)) {
            $body = $this->streamFactory->createStream($body);
        }
        if ($body !== null) {
            $request = $request->withBody($body);
        }

        return $this->sendRequest($request);
    }

    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }
}
