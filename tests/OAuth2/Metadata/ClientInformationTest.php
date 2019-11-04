<?php

namespace Tests\OAuth2\Metadata;

use OpenIDConnect\OAuth2\Metadata\ClientInformation;
use Tests\TestCase;

class ClientInformationTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeOkayWhenNewInstance(): void
    {
        $target = new ClientInformation([
            'some' => 'value',
        ]);

        $this->assertTrue($target->has('some'));
        $this->assertFalse($target->has('whatever'));
    }
}
