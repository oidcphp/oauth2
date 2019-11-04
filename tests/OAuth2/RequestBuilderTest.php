<?php

namespace Tests\OAuth2;

use Http\Factory\Guzzle\RequestFactory;
use OpenIDConnect\OAuth2\RequestBuilder;
use Tests\TestCase;

class RequestBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturn(): void
    {
        $target = (new RequestBuilder(new RequestFactory()))
            ->setProviderMetadata($this->createProviderMetadata())
            ->setClientInformation($this->createClientInformation());

        // base64_encode('some_id:some_secret')
        $exceptedAuthorization = 'Basic c29tZV9pZDpzb21lX3NlY3JldA==';

        $actual = $target->createTokenRequest([]);

        $this->assertSame('https://somewhere/token', (string)$actual->getUri());
        $this->assertSame('', (string)$actual->getBody());

        $this->assertTrue($actual->hasHeader('Authorization'));
        $this->assertStringContainsString($exceptedAuthorization, $actual->getHeaderLine('Authorization'));
        $this->assertStringContainsString('application/x-www-form-urlencoded', $actual->getHeaderLine('content-type'));
    }
}
