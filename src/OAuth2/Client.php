<?php declare(strict_types=1);

namespace OpenIDConnect\OAuth2;

use InvalidArgumentException;
use OpenIDConnect\OAuth2\Builder\AuthorizationFormResponseBuilder;
use OpenIDConnect\OAuth2\Builder\AuthorizationRedirectResponseBuilder;
use OpenIDConnect\OAuth2\Builder\TokenRequestBuilder;
use OpenIDConnect\OAuth2\Builder\UserInfoRequestBuilder;
use OpenIDConnect\OAuth2\ClientAuthentication\ClientAuthenticationAwareTrait;
use OpenIDConnect\OAuth2\Exceptions\OAuth2ClientException;
use OpenIDConnect\OAuth2\Exceptions\OAuth2ServerException;
use OpenIDConnect\OAuth2\Grant\AuthorizationCode;
use OpenIDConnect\OAuth2\Grant\GrantType;
use OpenIDConnect\OAuth2\Metadata\ClientInformation;
use OpenIDConnect\OAuth2\Metadata\ClientInformationAwaitTrait;
use OpenIDConnect\OAuth2\Metadata\ProviderMetadata;
use OpenIDConnect\OAuth2\Metadata\ProviderMetadataAwaitTrait;
use OpenIDConnect\OAuth2\Token\TokenFactoryInterface;
use OpenIDConnect\OAuth2\Token\TokenSetInterface;
use OpenIDConnect\Support\Container\EntryNotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * OpenID Connect Client
 */
class Client
{
    use ClientAuthenticationAwareTrait;
    use ClientInformationAwaitTrait;
    use ProviderMetadataAwaitTrait;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var null|string
     */
    private $state;

    /**
     * @param ProviderMetadata $providerMetadata
     * @param ClientInformation $clientRegistration
     * @param ContainerInterface $container The container implements PSR-11
     */
    public function __construct(
        ProviderMetadata $providerMetadata,
        ClientInformation $clientRegistration,
        ContainerInterface $container
    ) {
        $this->setProviderMetadata($providerMetadata);
        $this->setClientInformation($clientRegistration);
        $this->setContainer($container);
    }

    /**
     * Create PSR-7 response with form post
     *
     * @param array $parameters
     * @return ResponseInterface
     */
    public function createAuthorizeFormPostResponse(array $parameters = []): ResponseInterface
    {
        return (new AuthorizationFormResponseBuilder($this->container))
            ->setClientInformation($this->clientInformation)
            ->setProviderMetadata($this->providerMetadata)
            ->build($this->generateAuthorizationParameters($parameters));
    }

    /**
     * Create PSR-7 redirect response
     *
     * @param array $parameters
     * @return ResponseInterface
     */
    public function createAuthorizeRedirectResponse(array $parameters = []): ResponseInterface
    {
        return (new AuthorizationRedirectResponseBuilder($this->container))
            ->setClientInformation($this->clientInformation)
            ->setProviderMetadata($this->providerMetadata)
            ->build($this->generateAuthorizationParameters($parameters));
    }

    /**
     * @return null|string
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param array $parameters
     * @param array $checks
     * @return TokenSetInterface
     */
    public function handleCallback(array $parameters, array $checks = []): TokenSetInterface
    {
        if (!isset($parameters['code'])) {
            throw new InvalidArgumentException("'code' missing from the response");
        }

        if (isset($parameters['state']) && !isset($checks['state'])) {
            throw new InvalidArgumentException("'state' argument is missing");
        }

        if (!isset($parameters['state']) && isset($checks['state'])) {
            throw new OAuth2ClientException("'state' missing from the response");
        }

        if (isset($parameters['state'], $checks['state']) && ($checks['state'] !== $parameters['state'])) {
            $msg = "State mismatch, expected {$checks['state']}, got: {$parameters['state']}";
            throw new OAuth2ClientException($msg);
        }

        return $this->sendTokenRequest(new AuthorizationCode(), $parameters, $checks);
    }

    /**
     * @param GrantType $grant
     * @param array $parameters
     * @param array $checks
     * @return TokenSetInterface
     */
    public function sendTokenRequest(GrantType $grant, array $parameters = [], array $checks = []): TokenSetInterface
    {
        $parameters = $grant->prepareTokenRequestParameters(array_merge($parameters, $checks));

        $request = (new TokenRequestBuilder($this->container))
            ->setProviderMetadata($this->providerMetadata)
            ->setClientAuthentication($this->clientAuthentication)
            ->setClientInformation($this->clientInformation)
            ->build($grant, $parameters);

        /** @var ClientInterface $httpClient */
        $httpClient = $this->container->get(ClientInterface::class);

        try {
            $response = $httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            $msg = 'Token endpoint return error: ' . $e->getMessage();
            throw new OAuth2ServerException($msg, 0, $e);
        }

        $parsed = $this->parseTokenResponse($response);

        /** @var TokenFactoryInterface $tokenFactory */
        $tokenFactory = $this->container->get(TokenFactoryInterface::class);

        return $tokenFactory->create(array_merge($checks, $parsed));
    }

    /**
     * @param string $accessToken
     * @return array
     */
    public function sendUserInfoRequest(string $accessToken)
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->container->get(ClientInterface::class);

        $request = (new UserInfoRequestBuilder($this->container))
            ->setProviderMetadata($this->providerMetadata)
            ->setClientInformation($this->clientInformation)
            ->build($accessToken);

        $response = $httpClient->sendRequest($request);

        return json_decode((string)$response->getBody(), true);
    }

    /**
     * @param ContainerInterface $container
     * @return Client
     */
    public function setContainer(ContainerInterface $container): Client
    {
        $entries = [
            ClientInterface::class,
            StreamFactoryInterface::class,
            ResponseFactoryInterface::class,
            RequestFactoryInterface::class,
            UriFactoryInterface::class,
            TokenFactoryInterface::class,
        ];

        foreach ($entries as $entry) {
            if (!$container->has($entry)) {
                throw new EntryNotFoundException("The entry '$entry' is not found");
            }
        }

        $this->container = $container;

        return $this;
    }

    /**
     * Initial the authorization parameters
     *
     * @param array $options
     */
    public function initAuthorizationParameters(array $options = []): void
    {
        if (!empty($options['state'])) {
            $this->state = $options['state'];
        }

        if (null === $this->state) {
            $this->state = $this->generateRandomString();
        }
    }

    /**
     * Generate the random string
     *
     * @param int $length
     * @return string
     */
    protected function generateRandomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function generateAuthorizationParameters(array $parameters): array
    {
        $this->initAuthorizationParameters($parameters);

        $parameters['state'] = $this->state;

        if (empty($parameters['scope'])) {
            $parameters['scope'] = ['openid'];
        }

        $parameters += [
            'response_type' => 'code',
        ];

        if (is_array($parameters['scope'])) {
            $parameters['scope'] = implode(' ', $parameters['scope']);
        }

        if (!isset($parameters['redirect_uri'])) {
            $parameters['redirect_uri'] = $this->clientInformation->redirectUri();
        }

        $parameters['client_id'] = $this->clientInformation->id();

        return $parameters;
    }

    /**
     * Parse response from token endpoint
     *
     * @param ResponseInterface $response
     * @return array
     */
    private function parseTokenResponse(ResponseInterface $response): array
    {
        $content = (string)$response->getBody();

        // Just using JSON decode, See https://tools.ietf.org/html/rfc6749#section-5.1
        $parsed = json_decode($content, true);

        if (!is_array($parsed)) {
            throw new OAuth2ServerException('Invalid response received from token endpoint. Expected JSON.');
        }

        if (is_array($parsed) && !empty($parsed['error'])) {
            $error = $parsed['error'];

            throw new OAuth2ServerException($error);
        }

        return $parsed;
    }
}
