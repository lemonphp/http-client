<?php

namespace Tests\Transport;

use Lemon\Http\Client\Transport\MockTransport;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

/**
 * The test class for mockup transport
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class MockTransportTest extends TestCase
{
    /**
     * Test mockup should return fixed response
     *
     * @return void
     */
    public function testItShouldReturnFixedResponse()
    {
        $request = (new RequestFactory())->createRequest('GET', 'https://example.com');
        $response = (new ResponseFactory())->createResponse(200);

        $transport = new MockTransport($response);

        $this->assertSame($response, $transport->send($request));
    }
}
