<?php

namespace Tests;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response as HttpResponse;
use Http\Factory\Guzzle\RequestFactory;
use Http\Factory\Guzzle\ResponseFactory;
use Http\Factory\Guzzle\StreamFactory;
use Http\Factory\Guzzle\UriFactory;
use OpenIDConnect\OAuth2\Metadata\ClientInformation;
use OpenIDConnect\OAuth2\Metadata\ProviderMetadata;
use OpenIDConnect\OAuth2\Token\TokenFactory;
use OpenIDConnect\OAuth2\Token\TokenFactoryInterface;
use OpenIDConnect\Support\Container\Container;
use OpenIDConnect\Support\Http\GuzzlePsr18Client;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use function GuzzleHttp\json_encode;

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

    /**
     * Creates HTTP client.
     *
     * @param ResponseInterface|ResponseInterface[] $responses
     * @param array $history
     * @return HandlerStack
     */
    protected function createHandlerStack($responses = [], &$history = []): HandlerStack
    {
        if (!is_array($responses)) {
            $responses = [$responses];
        }

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push(Middleware::history($history));

        return $handler;
    }

    protected function createHttpClient($responses = [], &$history = []): ClientInterface
    {
        return new GuzzlePsr18Client(new HttpClient($this->createHttpMockOption($responses, $history)));
    }

    protected function createHttpJsonResponse(
        array $data = [],
        int $status = 200,
        array $headers = []
    ): ResponseInterface {
        return new HttpResponse($status, $headers, json_encode($data));
    }

    protected function createHttpMockOption($responses = [], &$history = []): array
    {
        return [
            'handler' => $this->createHandlerStack($responses, $history),
        ];
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
        return $this->createHttpJsonResponse($this->createFakeTokenSetParameter($overwrite), $status, $headers);
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
        if (empty($instances[ClientInterface::class])) {
            $instances[ClientInterface::class] = $this->createHttpClient();
        }

        if (empty($instances[StreamFactoryInterface::class])) {
            $instances[StreamFactoryInterface::class] = new StreamFactory();
        }

        if (empty($instances[ResponseFactoryInterface::class])) {
            $instances[ResponseFactoryInterface::class] = new ResponseFactory();
        }

        if (empty($instances[RequestFactoryInterface::class])) {
            $instances[RequestFactoryInterface::class] = new RequestFactory();
        }

        if (empty($instances[UriFactoryInterface::class])) {
            $instances[UriFactoryInterface::class] = new UriFactory();
        }

        if (empty($instances[TokenFactoryInterface::class])) {
            $instances[TokenFactoryInterface::class] = new TokenFactory();
        }

        return new Container($instances);
    }
}
