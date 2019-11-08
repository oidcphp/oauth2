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
     */
    public function assertHasKey(string $key): void
    {
        if (!$this->has($key)) {
            throw new DomainException("Missing parameter key: '{$key}'");
        }
    }

    /**
     * @param array $keys
     */
    public function assertHasKeys(array $keys): void
    {
        foreach ($keys as $key) {
            $this->assertHasKey($key);
        }
    }

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
     * @see \JsonSerializable
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param string $key
     * @return mixed
     * @throws DomainException
     */
    public function require(string $key)
    {
        $this->assertHasKey($key);

        return $this->get($key);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->parameters;
    }

    /**
     * Return a clone object with new value
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function with(string $key, $value)
    {
        $clone = clone $this;
        $clone->parameters[$key] = $value;

        return $clone;
    }
}
