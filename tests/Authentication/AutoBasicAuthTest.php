<?php

namespace Tests\Authentication;

use Lemon\Http\Client\Authentication\AutoBasicAuth;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\RequestFactory;

class AutoBasicAuthTest extends TestCase
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

    public function testItShouldAddAuthorizationHeader(): void
    {
        $request = $this->requestFactory->createRequest('GET', 'https://user:pass@example.com');

        $this->assertFalse($request->hasHeader('Authorization'));

        $request = (new AutoBasicAuth())->authenticate($request);
        $header = \sprintf('Basic %s', \base64_encode('user:pass'));

        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertEquals($header, $request->getHeaderLine('Authorization'));
        $this->assertEmpty($request->getUri()->getUserInfo());
    }

    public function testItShouldReplaceAuthorizationHeader(): void
    {
        $request = $this->requestFactory->createRequest('GET', 'https://user:pass@example.com')
            ->withHeader('Authorization', 'ABC');

        $this->assertTrue($request->hasHeader('Authorization'));

        $request = (new AutoBasicAuth())->authenticate($request);
        $header = \sprintf('Basic %s', \base64_encode('user:pass'));

        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertEquals($header, $request->getHeaderLine('Authorization'));
        $this->assertEmpty($request->getUri()->getUserInfo());
    }

    public function testItDosentShouldAddAuthorizationHeader(): void
    {
        $request = $this->requestFactory->createRequest('GET', 'https://example.com');

        $this->assertFalse($request->hasHeader('Authorization'));

        $request = (new AutoBasicAuth())->authenticate($request);

        $this->assertFalse($request->hasHeader('Authorization'));
    }

    public function testItDosentShouldRemoveUserInfo(): void
    {
        $request = $this->requestFactory->createRequest('GET', 'https://user:pass@example.com');

        $this->assertFalse($request->hasHeader('Authorization'));

        $request = (new AutoBasicAuth(false))->authenticate($request);
        $header = \sprintf('Basic %s', \base64_encode('user:pass'));

        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertEquals($header, $request->getHeaderLine('Authorization'));
        $this->assertEquals('user:pass', $request->getUri()->getUserInfo());
    }
}
