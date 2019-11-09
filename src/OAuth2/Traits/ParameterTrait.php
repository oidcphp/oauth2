<?php declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Traits;

use BadMethodCallException;
use DomainException;

trait ParameterTrait
{
    protected static $snakeCache = [];

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Convert a string to snake case
     *
     * @see https://github.com/laravel/framework/blob/v6.5.0/src/Illuminate/Support/Str.php#L525-L540
     * @param string $str
     * @return string
     */
    protected static function snake(string $str): string
    {
        $key = $str;

        if (isset(static::$snakeCache[$key])) {
            return static::$snakeCache[$key];
        }

        if (!ctype_lower($key)) {
            $key = (string)preg_replace('/\s+/u', '', ucwords($key));
            $key = (string)preg_replace('/(.)(?=[A-Z])/u', '$1' . '_', $key);
            $key = mb_strtolower($key, 'UTF-8');
        }

        return static::$snakeCache[$key] = $key;
    }

    public function __call($name, $arguments)
    {
        $key = static::snake($name);

        if ($this->has($key)) {
            return $this->get($key);
        }

        throw new BadMethodCallException("Undefined method '{$name}'");
    }

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
