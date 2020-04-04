<?php

namespace Tests\Handler;

use Lemon\Http\Client\Handler\ClientHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

/**
 * The client handler test
 *
 * @package     Tests\Handler
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class ClientHandlerTest extends TestCase
{
    /**
     * Test it should call client->sendRequest()
     *
     * @return void
     */
    public function testItShouldCallClientSendRequest()
    {
        $request = (new RequestFactory())->createRequest('GET', 'https://example.com');
        $response = (new ResponseFactory())->createResponse(200);

        $client = $this->getMockForAbstractClass(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $testHandler = new ClientHandler($client);
        $this->assertSame($response, $testHandler->handle($request));
    }
}
