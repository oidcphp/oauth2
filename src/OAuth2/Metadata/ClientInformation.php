<?php

declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Metadata;

use JsonSerializable;

/**
 * Client information
 *
 * @see https://tools.ietf.org/html/rfc7591#section-3.2.1
 */
class ClientInformation implements JsonSerializable
{
    use MetadataTraits;

    /**
     * @param array $metadata
     */
    public function __construct(array $metadata)
    {
        $this->metadata = $metadata;
    }
}
