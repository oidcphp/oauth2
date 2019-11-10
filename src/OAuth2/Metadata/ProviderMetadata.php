<?php declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Metadata;

use JsonSerializable;
use OpenIDConnect\OAuth2\Traits\ParameterTrait;

/**
 * OAuth 2.0 Authorization Server Metadata
 *
 * @method string issuer()
 * @method string authorizationEndpoint()
 * @method string jwksUri()
 * @method string registrationEndpoint()
 * @method array scopesSupported()
 * @method array responseTypesSupported()
 * @method array responseModesSupported()
 * @method array grantTypesSupported()
 * @method string tokenEndpoint()
 * @method array tokenEndpointAuthMethodsSupported()
 * @method array tokenEndpointAuthSigningAlgValuesSupported()
 * @method string revocationEndpoint()
 * @method array revocationEndpointAuthMethodsSupported()
 * @method array revocationEndpointAuthSigningAlgValuesSupported()
 * @method string introspectionEndpoint()
 * @method array introspectionEndpointAuthMethodsSupported()
 * @method array introspectionEndpointAuthSigningAlgValuesSupported()
 * @method array codeChallengeMethodsSupported() PKCE [RFC7636] code challenge methods supported
 * @method string serviceDocumentation() URL of a page containing human-readable information for developer
 * @method array uiLocalesSupported() Languages and scripts supported for the user interface
 * @method string opPolicyUri() URL that how the client can use the data provided
 * @method string opTosUri() URL that about the authorization server's terms of service
 *
 * @see https://tools.ietf.org/html/rfc8414#section-2
 */
class ProviderMetadata implements JsonSerializable
{
    use ParameterTrait;

    /**
     * @param array $metadata
     */
    public function __construct(array $metadata)
    {
        $this->parameters = $metadata;
    }
}
