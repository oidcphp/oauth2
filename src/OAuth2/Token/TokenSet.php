<?php

namespace OpenIDConnect\OAuth2\Token;

use DomainException;

class TokenSet implements TokenSetInterface
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * @param array $parameters An array from token endpoint response body
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @inheritDoc
     */
    public function accessToken(): string
    {
        return $this->require('access_token');
    }

    /**
     * @inheritDoc
     */
    public function expiresIn(): int
    {
        return $this->value('expires_in');
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return isset($this->parameters[$key]);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->parameters;
    }

    /**
     * @inheritDoc
     */
    public function refreshToken(): ?string
    {
        return $this->value('refresh_token');
    }

    /**
     * @inheritDoc
     */
    public function require(string $key)
    {
        if ($this->has($key)) {
            return $this->parameters[$key];
        }

        throw new DomainException("Missing key in TokenSet parameter '{$key}'");
    }

    /**
     * @inheritDoc
     */
    public function scope(): ?array
    {
        if (!$this->has('scope')) {
            return null;
        }

        if (is_array($this->parameters['scope'])) {
            return $this->parameters['scope'];
        }

        return explode(' ', $this->parameters['scope']);
    }

    /**
     * @inheritDoc
     */
    public function value(string $key, $default = null)
    {
        return $this->parameters[$key] ?? $default;
    }
}
