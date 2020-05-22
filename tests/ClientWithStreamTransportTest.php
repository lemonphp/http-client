<?php

namespace Tests;

use Lemon\Http\Client\Client;
use Lemon\Http\Client\Transport\StreamTransport;
use Psr\Http\Client\ClientInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

class ClientWithStreamTransportTest extends TestHttpClient
{
    protected $responseFactory;

    public function createClient(): ClientInterface
    {
        return new Client(new StreamTransport($this->streamFactory, $this->responseFactory));
    }

    protected function setUp(): void
    {
        $this->streamFactory = new StreamFactory();
        $this->requestFactory = new RequestFactory();
        $this->responseFactory = new ResponseFactory();
    }
}
