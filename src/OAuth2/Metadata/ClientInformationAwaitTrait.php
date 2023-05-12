<?php

declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Metadata;

trait ClientInformationAwaitTrait
{
    /**
     * @var ClientInformation
     */
    protected ClientInformation $clientInformation;

    /**
     * @param ClientInformation $clientInformation
     * @return static
     */
    public function setClientInformation(ClientInformation $clientInformation): static
    {
        $this->clientInformation = $clientInformation;

        return $this;
    }
}
