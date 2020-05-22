<?php

namespace Tests;

use Lemon\Http\Client\Authentication\BasicAuth;
use Lemon\Http\Client\Client;
use Lemon\Http\Client\Cookie\CookieJar;
use Lemon\Http\Client\History\Journal;
use Lemon\Http\Client\HttpMethodsClient;
use Lemon\Http\Client\Middleware\Authenticate;
use Lemon\Http\Client\Middleware\Cookie;
use Lemon\Http\Client\Middleware\History;
use Lemon\Http\Client\Middleware\Logging;
use Lemon\Http\Client\Middleware\SetBaseUri;
use Lemon\Http\Client\Middleware\UserAgent;
use Lemon\Http\Client\MiddlewareAwareClient;
use Lemon\Http\Client\Transport\StreamTransport;
use Psr\Http\Client\ClientInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UriFactory;

class CombinedClientTest extends TestHttpClient
{
    /**
     * @var \Psr\Http\Message\UriFactoryInterface
     */
    protected $uriFactory;

    /**
     * @var \Psr\Http\Message\ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @return \Psr\Http\Client\ClientInterface
     */
    public function createClient(): ClientInterface
    {
        $transport = new StreamTransport($this->streamFactory, $this->responseFactory);
        $client = new Client($transport);

        $middlewareChain = [
            new Logging(),
            new History(new Journal()),
            new Cookie(new CookieJar()),
            new UserAgent('Lemon/HtttpClient'),
            new Authenticate(new BasicAuth('test-user', '123456789')),
            new SetBaseUri($this->uriFactory->createUri('https://httpbin.org')),
        ];

        return new HttpMethodsClient(
            new MiddlewareAwareClient($client, $middlewareChain),
            $this->streamFactory,
            $this->requestFactory
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->uriFactory = new UriFactory();
        $this->streamFactory = new StreamFactory();
        $this->requestFactory = new RequestFactory();
        $this->responseFactory = new ResponseFactory();
    }
}
