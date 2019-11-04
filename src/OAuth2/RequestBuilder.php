<?php

declare(strict_types=1);

namespace OpenIDConnect\OAuth2;

use OpenIDConnect\OAuth2\ClientAuthentication\ClientAuthenticationAwareTrait;
use OpenIDConnect\OAuth2\Metadata\ClientInformationAwaitTrait;
use OpenIDConnect\OAuth2\Metadata\ProviderMetadataAwaitTrait;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

class RequestBuilder
{
    use ClientAuthenticationAwareTrait;
    use ClientInformationAwaitTrait;
    use ProviderMetadataAwaitTrait;

    /**
     * @param RequestFactoryInterface $baseRequestFactory
     */
    public function __construct(RequestFactoryInterface $baseRequestFactory)
    {
        $this->baseRequestFactory = $baseRequestFactory;
    }

    /**
     * @var RequestFactoryInterface
     */
    private $baseRequestFactory;

    /**
     * Create request for token endpoint
     *
     * @param array $parameters
     * @return RequestInterface
     */
    public function createTokenRequest(array $parameters): RequestInterface
    {
        // Decorate by token request factory
        $factory = new TokenRequestFactory($this->baseRequestFactory, $parameters);

        // Final Decorate class
        $final = $this->resolveClientAuthenticationByDefault(
            $this->clientInformation->id(),
            $this->clientInformation->secret()
        );

        return $final->setRequestFactory($factory)
            ->createRequest('POST', $this->providerMetadata->tokenEndpoint());
    }
}
