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
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class ClientHandlerTest extends TestCase
{
    /**
     * @var \Psr\Http\Message\RequestFactoryInterface
     */
    protected $requestFactory;

    /**
     * @var \Psr\Http\Message\ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->requestFactory = new RequestFactory();
        $this->responseFactory = new ResponseFactory();
    }

    /**
     * Test it should call client->sendRequest()
     *
     * @return void
     */
    public function testItShouldCallClientSendRequest()
    {
        $request = $this->requestFactory->createRequest('GET', 'https://example.com');
        $response = $this->responseFactory->createResponse(200);

        $client = $this->getMockForAbstractClass(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $testHandler = new ClientHandler($client);

        $this->assertSame($response, $testHandler->handle($request));
    }
}
