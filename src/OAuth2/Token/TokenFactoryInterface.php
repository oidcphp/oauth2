<?php declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Token;

interface TokenFactoryInterface
{
    /**
     * Create TokenSet by response from token endpoint
     *
     * @param array $parameters
     * @return TokenSetInterface
     */
    public function create(array $parameters): TokenSetInterface;
}
