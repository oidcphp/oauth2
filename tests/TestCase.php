<?php

namespace Tests;

use Illuminate\Container\Container;
use MilesChou\Mocker\GuzzleMocker;
use OpenIDConnect\OAuth2\Metadata\ClientInformation;
use OpenIDConnect\OAuth2\Metadata\ProviderMetadata;
use OpenIDConnect\OAuth2\Token\TokenFactory;
use OpenIDConnect\OAuth2\Token\TokenFactoryInterface;
use OpenIDConnect\Support\Laravel\HttpFactoryServiceProvider;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array $overwrite
     * @return ClientInformation
     */
    protected function createClientInformation($overwrite = []): ClientInformation
    {
        return new ClientInformation($this->createClientInformationConfig($overwrite));
    }

    /**
     * @param array $overwrite
     * @return array
     */
    protected function createClientInformationConfig($overwrite = []): array
    {
        return array_merge([
            'client_id' => 'some_id',
            'client_secret' => 'some_secret',
        ], $overwrite);
    }

    protected function createProviderMetadata($overwrite = []): ProviderMetadata
    {
        return new ProviderMetadata($this->createProviderMetadataConfig($overwrite));
    }

    protected function createProviderMetadataConfig($overwrite = []): array
    {
        return array_merge([
            'authorization_endpoint' => 'https://somewhere/authorization',
            'token_endpoint' => 'https://somewhere/token',
            'userinfo_endpoint' => 'https://somewhere/userinfo',
        ], $overwrite);
    }

    protected function createFakeTokenEndpointResponse($overwrite = [], $status = 200, $headers = []): ResponseInterface
    {
        return GuzzleMocker::createResponseByJson($this->createFakeTokenSetParameter($overwrite), $status, $headers);
    }

    protected function createFakeTokenSetParameter($overwrite = []): array
    {
        return array_merge([
            'access_token' => 'some-access-token',
            'expires_in' => 3600,
            'id_token' => null,
            'refresh_token' => 'some-refresh-token',
            'scope' => 'some-scope',
        ], $overwrite);
    }

    protected function createContainer(array $instances = []): ContainerInterface
    {
        $container = new Container();

        $container->singleton(ClientInterface::class, function () use ($instances) {
            if (empty($instances[ClientInterface::class])) {
                return GuzzleMocker::createPsr18Client();
            }

            return $instances[ClientInterface::class];
        });

        $container->singleton(TokenFactoryInterface::class, function () use ($instances) {
            if (empty($instances[TokenFactoryInterface::class])) {
                return new TokenFactory();
            }

            return $instances[TokenFactoryInterface::class];
        });

        (new HttpFactoryServiceProvider($container))->register();

        if (isset($instances[StreamFactoryInterface::class])) {
            $container->instance(StreamFactoryInterface::class, $instances[StreamFactoryInterface::class]);
        }

        if (isset($instances[ResponseFactoryInterface::class])) {
            $container->instance(ResponseFactoryInterface::class, $instances[ResponseFactoryInterface::class]);
        }

        if (isset($instances[RequestFactoryInterface::class])) {
            $container->instance(RequestFactoryInterface::class, $instances[RequestFactoryInterface::class]);
        }

        if (isset($instances[UriFactoryInterface::class])) {
            $container->instance(UriFactoryInterface::class, $instances[UriFactoryInterface::class]);
        }

        return $container;
    }
}
