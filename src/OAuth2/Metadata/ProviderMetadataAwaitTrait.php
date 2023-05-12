<?php

declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Metadata;

trait ProviderMetadataAwaitTrait
{
    /**
     * @var ProviderMetadata
     */
    protected ProviderMetadata $providerMetadata;

    /**
     * @param ProviderMetadata $providerMetadata
     * @return static
     */
    public function setProviderMetadata(ProviderMetadata $providerMetadata): static
    {
        $this->providerMetadata = $providerMetadata;

        return $this;
    }
}
