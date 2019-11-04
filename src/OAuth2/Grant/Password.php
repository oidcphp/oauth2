<?php

namespace OpenIDConnect\OAuth2\Grant;

/**
 * Resource owner password credentials grant
 *
 * @see http://tools.ietf.org/html/rfc6749#section-1.3.3
 */
class Password extends GrantType
{
    public function grantType(): string
    {
        return 'password';
    }

    protected function requiredParameters(): array
    {
        return [
            'username',
            'password',
        ];
    }
}
