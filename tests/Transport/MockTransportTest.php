<?php

namespace Tests\Transport;

use Lemon\Http\Client\Transport\MockTransport;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

class MockTransportTest extends TestCase
{
    public function testItShouldReturnFixedResponse()
    {
        $response = (new ResponseFactory())->createResponse(200, '');
        $request = (new RequestFactory())->createRequest('GET', 'https://example.com');

        $transport = new MockTransport($response);

        Assert::assertSame($response, $transport->send($request));
    }
}
