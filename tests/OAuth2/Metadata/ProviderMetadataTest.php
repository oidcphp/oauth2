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
            'some' => 'value',
        ]);

        $this->assertTrue($target->has('some'));
        $this->assertFalse($target->has('whatever'));
    }
}
