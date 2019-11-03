<?php

namespace Tests\OAuth2\ClientAuthentication;

use LogicException;
use OpenIDConnect\OAuth2\ClientAuthentication\ClientAuthentication;
use PHPUnit\Framework\TestCase;

class ClientAuthenticationTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowExceptionWhenNoBaseRequestFactory(): void
    {
        $this->expectException(LogicException::class);

        /** @var ClientAuthentication $target */
        $target = $this->getMockForAbstractClass(ClientAuthentication::class);

        $target->createRequest('POST', 'whatever');
    }
}
