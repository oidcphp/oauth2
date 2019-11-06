<?php

namespace Tests\OAuth2\Metadata;

use OpenIDConnect\OAuth2\Metadata\ProviderMetadata;
use Tests\TestCase;

class ProviderMetadataTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeOkayWhenNewInstance(): void
    {
        $target = new ProviderMetadata([
            'token_endpoint' => 'some-token-endpoint',
            'authorization_endpoint' => 'some-authorization-endpoint',
        ]);

        $this->assertSame('some-authorization-endpoint', $target->authorizationEndpoint());
        $this->assertSame('some-token-endpoint', $target->tokenEndpoint());
    }
}
