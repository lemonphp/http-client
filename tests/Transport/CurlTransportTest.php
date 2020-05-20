<?php

namespace Tests\Transport;

use Lemon\Http\Client\Options;
use Lemon\Http\Client\Transport\CurlTransport;
use Lemon\Http\Client\TransportInterface;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

class CurlTransportTest extends TestCase
{
    private function resolveTransport(): TransportInterface
    {
        return new CurlTransport(new StreamFactory(), new ResponseFactory(), new Options());
    }

    /**
     * Test transport should send request and return response
     *
     * @return void
     */
    public function testItShouldReturnResponse(): void
    {
        $transport = $this->resolveTransport();
        $request = (new RequestFactory())->createRequest('GET', 'https://httpbin.org/html');

        $response = $transport->send($request);

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
        $transport = $this->resolveTransport();
        $request = (new RequestFactory())
            ->createRequest(\strtoupper($method), 'https://httpbin.org/' . \strtolower($method))
            ->withHeader('Accept', 'application/json');

        $response = $transport->send($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-type'));
        $this->assertJson($response->getBody()->getContents());
    }

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
}
