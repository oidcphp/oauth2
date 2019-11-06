<?php

namespace Tests\OAuth2;

use OpenIDConnect\OAuth2\Grant\AuthorizationCode;
use OpenIDConnect\OAuth2\RequestBuilder;
use Tests\TestCase;

class RequestBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturn(): void
    {
        $target = (new RequestBuilder($this->createContainer()))
            ->setProviderMetadata($this->createProviderMetadata())
            ->setClientInformation($this->createClientInformation());

        // base64_encode('some_id:some_secret')
        $exceptedAuthorization = 'Basic c29tZV9pZDpzb21lX3NlY3JldA==';

        $actual = $target->createTokenRequest(new AuthorizationCode(), [
            'code' => 'some-code',
            'redirect_uri' => 'some-redirect-uri',
        ]);

        $this->assertSame('https://somewhere/token', (string)$actual->getUri());
        $this->assertStringContainsString('grant_type=authorization_code', (string)$actual->getBody());
        $this->assertStringContainsString('code=some-code', (string)$actual->getBody());
        $this->assertStringContainsString('redirect_uri=some-redirect-uri', (string)$actual->getBody());

        $this->assertTrue($actual->hasHeader('Authorization'));
        $this->assertStringContainsString($exceptedAuthorization, $actual->getHeaderLine('Authorization'));
        $this->assertStringContainsString('application/x-www-form-urlencoded', $actual->getHeaderLine('content-type'));
    }
}
