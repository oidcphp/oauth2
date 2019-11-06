<?php

declare(strict_types=1);

namespace OpenIDConnect\OAuth2;

use OpenIDConnect\OAuth2\ClientAuthentication\ClientAuthenticationAwareTrait;
use OpenIDConnect\OAuth2\Grant\GrantType;
use OpenIDConnect\OAuth2\Metadata\ClientInformationAwaitTrait;
use OpenIDConnect\OAuth2\Metadata\ProviderMetadataAwaitTrait;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

class RequestBuilder
{
    use ClientAuthenticationAwareTrait;
    use ClientInformationAwaitTrait;
    use ProviderMetadataAwaitTrait;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Create request for token endpoint
     *
     * @param GrantType $grantType
     * @param array $parameters
     * @return RequestInterface
     */
    public function createTokenRequest(GrantType $grantType, array $parameters): RequestInterface
    {
        $parameters = $grantType->prepareRequestParameters($parameters);

        // Decorate by token request factory
        $factory = new TokenRequestFactory($this->container->get(RequestFactoryInterface::class), $parameters);

        // Final Decorate class
        $final = $this->resolveClientAuthenticationByDefault(
            $this->clientInformation->id(),
            $this->clientInformation->secret()
        );

        return $final->processRequest($factory->createRequest('POST', $this->providerMetadata->tokenEndpoint()));
    }
}
