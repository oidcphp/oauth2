<?php

namespace Tests\OAuth2;

use Http\Factory\Guzzle\RequestFactory;
use OpenIDConnect\OAuth2\TokenRequestFactory;
use Tests\TestCase;

class TokenRequestFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnTokenRequestWithParameters(): void
    {
        $target = new TokenRequestFactory(new RequestFactory(), [
            'grant_type' => 'authorization_code',
        ]);

        $actual = $target->createRequest('POST', 'https://somewhere/token');

        $this->assertSame('https://somewhere/token', (string)$actual->getUri());
        $this->assertSame('grant_type=authorization_code', (string)$actual->getBody());
        $this->assertStringContainsString('application/x-www-form-urlencoded', $actual->getHeaderLine('content-type'));
    }
}
