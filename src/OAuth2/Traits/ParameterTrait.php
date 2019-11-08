<?php declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Traits;

use DomainException;

trait ParameterTrait
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->parameters[$key] ?? $default;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->parameters[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     * @throws DomainException
     */
    public function require(string $key)
    {
        if ($this->has($key)) {
            return $this->parameters[$key];
        }

        throw new DomainException("Missing key in TokenSet parameter '{$key}'");
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->parameters;
    }
}
