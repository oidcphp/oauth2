<?php

declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Metadata;

use JsonSerializable;

/**
 * Client metadata
 *
 * @see https://tools.ietf.org/html/rfc7591#section-2
 */
class ClientMetadata implements JsonSerializable
{
    use MetadataTraits;

    /**
     * @param array $metadata
     */
    public function __construct(array $metadata = [])
    {
        $this->metadata = $metadata;
    }
}
