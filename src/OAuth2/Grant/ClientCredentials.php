<?php

namespace OpenIDConnect\OAuth2\Grant;

/**
 * Client credentials grant
 *
 * @see http://tools.ietf.org/html/rfc6749#section-1.3.4
 */
class ClientCredentials extends GrantType
{
    public function grantType(): string
    {
        return 'client_credentials';
    }

    protected function requiredParameters(): array
    {
        return [];
    }
}
