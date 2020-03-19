<?php

namespace Tests;

use Lemon\Http\Client\Client;
use Lemon\Http\Client\Transport\MockTransport;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

class ClientTest extends TestCase
{
    public function testItShouldSendRequest()
    {
        $response = (new ResponseFactory())->createResponse(200, '');
        $transport = new MockTransport($response);
        $request = (new RequestFactory())->createRequest('GET', 'https://example.com');

        $client = new Client($transport);

        Assert::assertSame($response, $client->sendRequest($request));
    }
}
