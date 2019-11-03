<?php

namespace Tests\OAuth2\ClientAuthentication;

use Http\Factory\Guzzle\RequestFactory;
use OpenIDConnect\OAuth2\ClientAuthentication\ClientSecretPost;
use PHPUnit\Framework\TestCase;

class ClientSecretPostTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnClientSecretParameter(): void
    {
        $target = new ClientSecretPost('c', 's');
        $target->setRequestFactory(new RequestFactory());

        $actual = $target->createRequest('GET', 'whatever');

        $this->assertStringContainsString('client_id=c', (string)$actual->getBody());
        $this->assertStringContainsString('client_secret=s', (string)$actual->getBody());
    }
}
