<?php

namespace Lemon\Http\Client\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;
use Psr\Http\Message\MessageInterface;

class Logging implements MiddlewareInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  \Lemon\Http\Client\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $log =
            '>>>>>> Request' . \PHP_EOL . \PHP_EOL .
            $this->formatRequestLog($request) . \PHP_EOL . \PHP_EOL .
            '<<<<<< Response' . \PHP_EOL . \PHP_EOL .
            $this->formatResponseLog($response) . \PHP_EOL;

        $this->logger->info($log);

        return $response;
    }

    /**
     * Format request log.
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @return string
     */
    protected function formatRequestLog(RequestInterface $request): string
    {
        $uri = $request->getUri();

        // Main header
        $log = \sprintf(
            '%s %s HTTP/%s',
            $request->getMethod(),
            $uri->getPath() . (!empty($uri->getQuery()) ? '?' . $uri->getQuery() : ''),
            $request->getProtocolVersion()
        );

        // Host
        $log .= \sprintf('Host: %s', $uri->getHost());
        if ($uri->getPort()) {
            $log .= \sprintf(':%d', $uri->getPort());
        }
        $log .= \PHP_EOL;

        // Message
        $log .= $this->formatMessageLog($request);

        return $log;
    }

    /**
     * Format response log.
     *
     * @param  \Psr\Http\Message\ResponseInterface|null $response
     * @return string
     */
    protected function formatResponseLog(?ResponseInterface $response): string
    {
        if (null === $response) {
            return 'No response';
        }

        // Main header
        $log = \sprintf(
            'HTTP/%s %s %s' . \PHP_EOL,
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        // Message
        $log .= $this->formatMessageLog($response);

        return $log;
    }

    /**
     * Format message log.
     *
     * @param  \Psr\Http\Message\MessageInterface $message
     * @return string
     */
    protected function formatMessageLog(MessageInterface $message): string
    {
        $log = '';

        // Headers
        foreach ($message->getHeaders() as $key => $values) {
            foreach ($values as $value) {
                $log .= \sprintf('%s: %s' . \PHP_EOL, $key, $value);
            }
        }

        // Body
        $log .= \PHP_EOL . ($message->getBody()->getSize() > 0 ? $message->getBody() : 'Empty body');

        return $log;
    }
}
