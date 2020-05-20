<?php

namespace Tests\Authentication;

use Lemon\Http\Client\Authentication\BasicAuth;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\RequestFactory;

class BasicAuthTest extends TestCase
{
    public function testItShouldAuthenticateRequest(): void
    {
        $request = (new RequestFactory())->createRequest('GET', 'https://httpbin.org/basic-auth/test-user/123456789')
            ->withHeader('Accept', 'application/json');

        $this->assertEmpty($request->getHeaderLine('Authorization'));

        $request = (new BasicAuth('test-user', '123456789'))->authenticate($request);

        $this->assertNotEmpty($request->getHeaderLine('Authorization'));

        $header = $request->getHeaderLine('Authorization');
        $this->assertSame(\sprintf('Basic %s', \base64_encode('test-user:123456789')), $header);
    }
}
