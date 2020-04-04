<?php

namespace Tests;

use Lemon\Http\Client\Client;
use Lemon\Http\Client\ClientOptions;
use Lemon\Http\Client\TransportInterface;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

/**
 * The HTTP client test
 *
 * @package     Tests
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class ClientTest extends TestCase
{
    /**
     * Test client should send request
     *
     * @return void
     */
    public function testItShouldSendRequest()
    {
        $request = (new RequestFactory())->createRequest('GET', 'https://example.com');
        $response = (new ResponseFactory())->createResponse(200);

        $transport = $this->getMockForAbstractClass(TransportInterface::class);
        $transport->expects($this->once())
            ->method('send')
            ->with($request)
            ->willReturn($response);

        $client = new Client($transport);

        $this->assertSame($response, $client->sendRequest($request));
    }
}
