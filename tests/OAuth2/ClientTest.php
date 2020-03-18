<?php

namespace Tests\OAuth2;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Exception\NetworkException;
use InvalidArgumentException;
use OpenIDConnect\OAuth2\Client;
use OpenIDConnect\OAuth2\Exceptions\OAuth2ClientException;
use OpenIDConnect\OAuth2\Exceptions\OAuth2ServerException;
use OpenIDConnect\OAuth2\Grant\AuthorizationCode;
use Psr\Http\Client\ClientInterface;
use Tests\TestCase;

class ClientTest extends TestCase
{
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

        $this->assertStringContainsString('action="https://somewhere/authorization"', (string)$actual->getBody());
        $this->assertStringContainsString('name="state"', (string)$actual->getBody());
        $this->assertStringContainsString('name="response_type" value="code"', (string)$actual->getBody());
        $this->assertStringContainsString(
            'name="redirect_uri" value="https://someredirect"',
            (string)$actual->getBody()
        );
        $this->assertStringContainsString('name="client_id" value="some_id"', (string)$actual->getBody());
    }

    /**
     * @test
     */
    public function shouldReturnRedirectWhenCallCreateRedirect(): void
    {
        $target = new Client(
            $this->createProviderMetadata(),
            $this->createClientInformation([
                'redirect_uri' => 'https://someredirect',
            ]),
            $this->createContainer()
        );

        $actual = $target->createAuthorizeRedirectResponse();

        $actualLocation = $actual->getHeaderLine('Location');

        $this->assertStringStartsWith('https://somewhere/authorization', $actualLocation);
        $this->assertStringContainsString('state=', $actualLocation);
        $this->assertStringContainsString('response_type=code', $actualLocation);
        $this->assertStringContainsString('redirect_uri=' . rawurlencode('https://someredirect'), $actualLocation);
        $this->assertStringContainsString('client_id=some_id', $actualLocation);
    }

    /**
     * @test
     */
    public function shouldReturnTokenSetInstanceWhenCallSendTokenRequest(): void
    {
        $target = new Client(
            $this->createProviderMetadata(),
            $this->createClientInformation([
                'redirect_uri' => 'https://someredirect',
            ]),
            $this->createContainer([
                ClientInterface::class => $this->createHttpClient($this->createFakeTokenEndpointResponse([
                    'access_token' => 'some-access-token',
                ])),
            ])
        );

        $actual = $target->sendTokenRequest(new AuthorizationCode(), [
            'code' => 'whatever',
            'redirect_uri' => 'whatever',
        ], []);

        $this->assertSame('some-access-token', $actual->accessToken());
    }

    /**
     * @test
     */
    public function shouldThrowOAuth2ServerExceptionWhenCatchRequestException(): void
    {
        $this->expectException(OAuth2ServerException::class);

        $target = new Client(
            $this->createProviderMetadata(),
            $this->createClientInformation([
                'redirect_uri' => 'https://someredirect',
            ]),
            $this->createContainer([
                ClientInterface::class => $this->createHttpClient([
                    new NetworkException('whatever', new Request('GET', 'whatever')),
                ]),
            ])
        );

        $target->sendTokenRequest(new AuthorizationCode(), [
            'code' => 'whatever',
            'redirect_uri' => 'whatever',
        ], []);
    }

    /**
     * @test
     */
    public function shouldThrowOAuth2ServerExceptionWhenPayloadIsNotJson(): void
    {
        $this->expectException(OAuth2ServerException::class);

        $target = new Client(
            $this->createProviderMetadata(),
            $this->createClientInformation([
                'redirect_uri' => 'https://someredirect',
            ]),
            $this->createContainer([
                ClientInterface::class => $this->createHttpClient([
                    new Response(200, [], 'whatever'),
                ]),
            ])
        );

        $target->sendTokenRequest(new AuthorizationCode(), [
            'code' => 'whatever',
            'redirect_uri' => 'whatever',
        ], []);
    }

    /**
     * @test
     */
    public function shouldThrowOAuth2ServerExceptionWhenReturnJsonHasErrorKey(): void
    {
        $this->expectException(OAuth2ServerException::class);

        $target = new Client(
            $this->createProviderMetadata(),
            $this->createClientInformation([
                'redirect_uri' => 'https://someredirect',
            ]),
            $this->createContainer([
                ClientInterface::class => $this->createHttpClient([
                    $this->createHttpJsonResponse(['error' => 'whatever']),
                ]),
            ])
        );

        $target->sendTokenRequest(new AuthorizationCode(), [
            'code' => 'whatever',
            'redirect_uri' => 'whatever',
        ], []);
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentExceptionWhenHandleCallbackWithParameterMissingCode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $target = new Client(
            $this->createProviderMetadata(),
            $this->createClientInformation(),
            $this->createContainer()
        );

        $target->handleCallback([]);
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentExceptionWhenHandleCallbackWithParameterGivenStateButChecksNot(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $target = new Client(
            $this->createProviderMetadata(),
            $this->createClientInformation(),
            $this->createContainer([
                ClientInterface::class => $this->createHttpClient($this->createFakeTokenEndpointResponse([
                    'access_token' => 'some-access-token',
                ])),
            ])
        );

        $target->handleCallback([
            'code' => 'whatever',
            'redirect_uri' => 'whatever',
            'state' => 'whatever',
        ]);
    }

    /**
     * @test
     */
    public function shouldThrowOAuth2ClientExceptionWhenHandleCallbackWithChecksGivenStateButParametersNot(): void
    {
        $this->expectException(OAuth2ClientException::class);

        $target = new Client(
            $this->createProviderMetadata(),
            $this->createClientInformation(),
            $this->createContainer([
                ClientInterface::class => $this->createHttpClient($this->createFakeTokenEndpointResponse([
                    'access_token' => 'some-access-token',
                ])),
            ])
        );

        $target->handleCallback([
            'code' => 'whatever',
            'redirect_uri' => 'whatever',
        ], [
            'state' => 'whatever',
        ]);
    }

    /**
     * @test
     */
    public function shouldThrowOAuth2ClientExceptionWhenHandleCallbackWithBothGivenButNotSame(): void
    {
        $this->expectException(OAuth2ClientException::class);

        $target = new Client(
            $this->createProviderMetadata(),
            $this->createClientInformation(),
            $this->createContainer([
                ClientInterface::class => $this->createHttpClient($this->createFakeTokenEndpointResponse([
                    'access_token' => 'some-access-token',
                ])),
            ])
        );

        $target->handleCallback([
            'code' => 'whatever',
            'redirect_uri' => 'whatever',
            'state' => 'foo',
        ], [
            'state' => 'bar',
        ]);
    }

    /**
     * @test
     */
    public function shouldReturnTokenWhenHandleCallbackWithStateIsNotGiven(): void
    {
        $target = new Client(
            $this->createProviderMetadata(),
            $this->createClientInformation(),
            $this->createContainer([
                ClientInterface::class => $this->createHttpClient($this->createFakeTokenEndpointResponse([
                    'access_token' => 'some-access-token',
                ])),
            ])
        );

        $actual = $target->handleCallback([
            'code' => 'whatever',
            'redirect_uri' => 'whatever',
        ]);

        $this->assertSame('some-access-token', $actual->accessToken());
    }

    /**
     * @test
     */
    public function shouldReturnTokenWhenHandleCallbackWithBothGivenAndSame(): void
    {
        $target = new Client(
            $this->createProviderMetadata(),
            $this->createClientInformation(),
            $this->createContainer([
                ClientInterface::class => $this->createHttpClient($this->createFakeTokenEndpointResponse([
                    'access_token' => 'some-access-token',
                ])),
            ])
        );

        $actual = $target->handleCallback([
            'code' => 'whatever',
            'redirect_uri' => 'whatever',
            'state' => 'foo',
        ], [
            'state' => 'foo',
        ]);

        $this->assertSame('some-access-token', $actual->accessToken());
    }
}
