<?php declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Metadata;

use JsonSerializable;
use OpenIDConnect\OAuth2\Traits\ParameterTrait;

/**
 * OAuth 2.0 Authorization Server Metadata
 *
 * @method string authorizationEndpoint() URL of the authorization server's authorization endpoint.
 * @method string tokenEndpoint() URL of the authorization server's token endpoint.
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
