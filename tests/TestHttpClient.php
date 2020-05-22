<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

abstract class TestHttpClient extends TestCase
{
    /**
     * @var \Psr\Http\Message\RequestFactoryInterface
     */
    protected $requestFactory;

    /**
     * @var \Psr\Http\Message\StreamFactoryInterface
     */
    protected $streamFactory;

    /**
     * @return \Psr\Http\Client\ClientInterface
     */
    abstract protected function createClient(): ClientInterface;

    /**
     * Provide Http method list
     *
     * @return array
     */
    public function httpMethods(): array
    {
        return [
            ['GET'],
            ['POST'],
            ['PUT'],
            ['PATCH'],
            ['DELETE'],
        ];
    }

    /**
     * Test transport should send request and return response
     *
     * @return void
     */
    public function testItShouldReturnResponse(): void
    {
        $client = $this->createClient();
        $request = $this->requestFactory
            ->createRequest('GET', 'https://httpbin.org/html');

        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['text/html; charset=utf-8'], $response->getHeader('Content-type'));
    }

    /**
     * Test it can send request with Http method
     *
     * @param  string $method
     * @return void
     * @dataProvider httpMethods
     */
    public function testItShouldSendWithSpecifiedHttpMethod(string $method): void
    {
        $client = $this->createClient();
        $request = $this->requestFactory
            ->createRequest(\strtoupper($method), 'https://httpbin.org/' . \strtolower($method));

        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-type'));
        $this->assertJson($response->getBody()->__toString());
    }

    /**
     * Test it should send json
     *
     * @return void
     */
    public function testItShouldSendJson(): void
    {
        $postData = ['foo' => 'bar'];
        $client = $this->createClient();
        $request = $this->requestFactory
            ->createRequest('POST', 'https://httpbin.org/post')
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream(\json_encode($postData)));

        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-type'));
        $this->assertJson($response->getBody()->__toString());

        $contents = \json_decode($response->getBody()->__toString(), true);
        $this->assertEquals($postData, $contents['json']);
    }

    /**
     * Test it should send form data
     *
     * @return void
     */
    public function testItShouldSendFormData(): void
    {
        $postData = ['foo' => 'bar'];
        $client = $this->createClient();
        $request = $this->requestFactory
            ->createRequest('POST', 'https://httpbin.org/post')
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($this->streamFactory->createStream(\http_build_query($postData)));

        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-type'));
        $this->assertJson($response->getBody()->__toString());

        $contents = \json_decode($response->getBody()->__toString(), true);
        $this->assertEquals($postData, $contents['form']);
    }

    /**
     * Test it should send text plain data
     *
     * @return void
     */
    public function testItShouldSendTextPlainData(): void
    {
        $postData = 'Test data';
        $client = $this->createClient();
        $request = $this->requestFactory
            ->createRequest('POST', 'https://httpbin.org/post')
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'text/plain')
            ->withBody($this->streamFactory->createStream($postData));

        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-type'));
        $this->assertJson($response->getBody()->__toString());

        $contents = \json_decode($response->getBody()->__toString(), true);
        $this->assertEquals($postData, $contents['data']);
    }
}
