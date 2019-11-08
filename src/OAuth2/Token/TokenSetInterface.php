<?php declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Token;

use DomainException;
use JsonSerializable;

/**
 * The token set interface for OpenID Connect flow
 */
interface TokenSetInterface extends JsonSerializable
{
    /**
     * @see https://tools.ietf.org/html/rfc6749#section-1.4
     * @return string
     */
    public function accessToken(): string;

    /**
     * @see https://tools.ietf.org/html/rfc6749#section-5.1
     * @return int|null
     */
    public function expiresIn(): ?int;

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @see https://tools.ietf.org/html/rfc6749#section-1.5
     * @return string|null
     */
    public function refreshToken(): ?string;

    /**
     * Require key and return the value
     *
     * @param string $key Given null will get all value
     * @return mixed
     * @throws DomainException Throw when the key is not exist
     */
    public function require(string $key);

    /**
     * @see https://tools.ietf.org/html/rfc6749#section-5.1
     * @return array|null
     */
    public function scope(): ?array;

    /**
     * Returns additional vendor values stored in the token.
     *
     * @param string $key Given null will get all value
     * @param mixed $default Return default when the key is not found
     * @return mixed
     */
    public function value(string $key, $default = null);
}
