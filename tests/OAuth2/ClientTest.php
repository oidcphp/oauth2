<?php

namespace Tests\OAuth2;

use OpenIDConnect\OAuth2\Client;
use OpenIDConnect\Support\Container\Container;
use OpenIDConnect\Support\Container\EntryNotFoundException;
use Tests\TestCase;

class ClientTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowExceptionWhenContainerIsMissingDefinedClass(): void
    {
        $this->expectException(EntryNotFoundException::class);

        new Client(
            $this->createProviderMetadata(),
            $this->createClientInformation([
                'redirect_uri' => 'https://someredirect',
            ]),
            new Container()
        );
    }

    /**
     * @test
     */
    public function shouldReturnPreparedStateWhenInitParameters(): void
    {
        $target = new Client(
            $this->createProviderMetadata(),
            $this->createClientInformation([
                'redirect_uri' => 'https://someredirect',
            ]),
            $this->createContainer()
        );

        $target->initAuthorizationParameters([
            'state' => 'expected-state',
        ]);

        $this->assertSame('expected-state', $target->getState());
    }

    /**
     * @test
     */
    public function shouldReturnHtmlWhenCallCreateFormPost(): void
    {
        $target = new Client(
            $this->createProviderMetadata(),
            $this->createClientInformation([
                'redirect_uri' => 'https://someredirect',
            ]),
            $this->createContainer()
        );

        $actual = $target->createAuthorizeFormPostResponse();

        $this->assertStringContainsStringIgnoringCase(
            'action="https://somewhere/authorization"',
            (string)$actual->getBody()
        );
        $this->assertStringContainsStringIgnoringCase('name="state"', (string)$actual->getBody());
        $this->assertStringContainsStringIgnoringCase('name="response_type" value="code"', (string)$actual->getBody());
        $this->assertStringContainsStringIgnoringCase(
            'name="redirect_uri" value="https://someredirect"',
            (string)$actual->getBody()
        );
        $this->assertStringContainsStringIgnoringCase('name="client_id" value="some_id"', (string)$actual->getBody());
    }
}
