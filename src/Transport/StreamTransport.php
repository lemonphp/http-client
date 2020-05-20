<?php

namespace Lemon\Http\Client\Transport;

use Lemon\Http\Client\Exception\NetworkException;
use Lemon\Http\Client\Exception\RequestException;
use Lemon\Http\Client\Helper;
use Lemon\Http\Client\TransportInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class StreamTransport implements TransportInterface
{
    /**
     * @var \Psr\Http\Message\StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * @var \Psr\Http\Message\ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var \Lemon\Http\Client\Options
     */
    private $options;

    /**
     * @param \Psr\Http\Message\StreamFactoryInterface $streamFactory
     * @param \Psr\Http\Message\ResponseFactoryInterface $responseFactory
     * @param \Lemon\Http\Client\Options $options
     */
    public function __construct(
        StreamFactoryInterface $streamFactory,
        ResponseFactoryInterface $responseFactory,
        $options = []
    ) {
        $this->streamFactory = $streamFactory;
        $this->responseFactory = $responseFactory;
        $this->options = $options;
    }

    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        $context = [
            'http' => [
                'method' => $request->getMethod(),
                'header' => Helper::serializeHeadersFromPsr7Format($request->getHeaders()),
                'protocol_version' => $request->getProtocolVersion(),
                'ignore_errors' => true,
                'timeout' => $this->options->timeout,
                'follow_location' => $this->options->follow_location,
            ],
        ];

        if ($request->getBody()->getSize()) {
            $context['http']['content'] = $request->getBody()->__toString();
        }

        $resource = \fopen($request->getUri()->__toString(), 'rb', false, \stream_context_create($context));

        if (!\is_resource($resource)) {
            $error = error_get_last()['message'];
            if (\strpos($error, 'getaddrinfo') !== false || \strpos($error, 'Connection refused') !== false) {
                $e = new NetworkException($request, $error);
            } else {
                $e = new RequestException($request, $error);
            }

            throw $e;
        }

        $stream = Helper::copyResourceToStream($resource, $this->streamFactory);

        $headers = \stream_get_meta_data($resource)['wrapper_data'] ?? [];

        if ($this->options->follow_location) {
            $headers = Helper::filterLastResponseHeaders($headers);
        }

        \fclose($resource);

        $parts = \explode(' ', \array_shift($headers), 3);
        $version = \explode('/', $parts[0])[1];
        $status = (int) $parts[1];

        $response = $this->responseFactory->createResponse($status)
            ->withProtocolVersion($version)
            ->withBody($stream);

        foreach (Helper::deserializeHeadersToPsr7Format($headers) as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response;
    }
}
