<?php

namespace OpenIDConnect\OAuth2\Grant;

/**
 * Authorization code grant.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-1.3.1
 */
class AuthorizationCode extends GrantType
{
    public function grantType(): string
    {
        return 'authorization_code';
    }

    protected function requiredParameters(): array
    {
        return [
            'code',
        ];
    }
}
