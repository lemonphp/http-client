<?php

namespace Tests\Authentication;

use Lemon\Http\Client\Authentication\BasicAuth;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\RequestFactory;

class BasicAuthTest extends TestCase
{
    /**
     * @var \Psr\Http\Message\RequestFactoryInterface
     */
    protected $requestFactory;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->requestFactory = new RequestFactory();
    }

    public function testItShouldAuthenticateRequest(): void
    {
        $request = $this->requestFactory->createRequest('GET', 'https://example.com');

        $this->assertFalse($request->hasHeader('Authorization'));

        $request = (new BasicAuth('user', 'pass'))->authenticate($request);
        $header = \sprintf('Basic %s', \base64_encode('user:pass'));

        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertEquals($header, $request->getHeaderLine('Authorization'));
    }
}
