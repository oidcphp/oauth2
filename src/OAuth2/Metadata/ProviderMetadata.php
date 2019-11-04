<?php

declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Metadata;

use JsonSerializable;

/**
 * OAuth 2.0 Authorization Server Metadata
 *
 * @see https://tools.ietf.org/html/rfc8414#section-2
 */
class ProviderMetadata implements JsonSerializable
{
    use MetadataTraits;

    /**
     * @param array $metadata
     */
    public function __construct(array $metadata)
    {
        $this->metadata = $metadata;
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
