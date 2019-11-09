<?php

namespace Tests\OAuth2\Builder;

use OpenIDConnect\OAuth2\Builder\TokenRequestBuilder;
use OpenIDConnect\OAuth2\Builder\UserInfoRequestBuilder;
use OpenIDConnect\OAuth2\Exceptions\OAuth2ServerException;
use OpenIDConnect\OAuth2\Grant\AuthorizationCode;
use OpenIDConnect\OAuth2\Metadata\ProviderMetadata;
use Tests\TestCase;

class UserInfoRequestBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnCorrectRequestInstance(): void
    {
        $target = (new UserInfoRequestBuilder($this->createContainer()))
            ->setProviderMetadata($this->createProviderMetadata())
            ->setClientInformation($this->createClientInformation());

        $actual = $target->build('some-access-token');

        $this->assertSame('https://somewhere/userinfo', (string)$actual->getUri());
        $this->assertTrue($actual->hasHeader('Authorization'));
        $this->assertStringContainsString('Bearer some-access-token', $actual->getHeaderLine('Authorization'));
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenProviderDoesNotSupportUserInfoEndpoint(): void
    {
        $this->expectException(OAuth2ServerException::class);

        $target = (new UserInfoRequestBuilder($this->createContainer()))
            ->setProviderMetadata(new ProviderMetadata([]))
            ->setClientInformation($this->createClientInformation());

        $target->build('some-access-token');
    }
}
