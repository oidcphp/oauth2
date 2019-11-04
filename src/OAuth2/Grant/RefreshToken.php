<?php

namespace OpenIDConnect\OAuth2\Grant;

/**
 * Represents a refresh token grant.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-6
 */
class RefreshToken extends GrantType
{
    public function grantType(): string
    {
        return 'refresh_token';
    }

    protected function requiredParameters(): array
    {
        return [
            'refresh_token',
        ];
    }
}
