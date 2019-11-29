<?php

namespace Tests\OAuth2\Metadata;

use Tests\TestCase;

/**
 * @covers \OpenIDConnect\OAuth2\Metadata\ProviderMetadata
 */
class ProviderMetadataTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeEmptyWhenNoJwk(): void
    {
        $target = $this->createProviderMetadata();

        $this->assertSame(['keys' => []], $target->jwkSet()->toArray());
    }

    /**
     * @test
     */
    public function shouldSaveOneKeyWhenAddJwk(): void
    {
        $target = $this->createProviderMetadata();
        $target->addJwk(['kid' => 'whatever']);

        $this->assertSame(['keys' => [['kid' => 'whatever']]], $target->jwkSet()->toArray());
    }
}
