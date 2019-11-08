<?php declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Metadata;

use JsonSerializable;
use OpenIDConnect\OAuth2\Traits\ParameterTrait;

/**
 * OAuth 2.0 Authorization Server Metadata
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

    /**
     * URL of the authorization server's authorization endpoint
     *
     * This is REQUIRED unless no grant types are supported that use the authorization endpoint.
     *
     * @return mixed
     */
    public function authorizationEndpoint()
    {
        return $this->get('authorization_endpoint');
    }

    /**
     * URL of the authorization server's token endpoint.
     *
     * This is REQUIRED unless only the implicit grant type is supported.
     *
     * @return mixed
     */
    public function tokenEndpoint()
    {
        return $this->get('token_endpoint');
    }
}
