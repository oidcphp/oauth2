<?php

declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Metadata;

use RuntimeException;

trait MetadataTraits
{
    /**
     * @var array
     */
    private $metadata = [];

    /**
     * @param string $key
     */
    public function assertHasKey(string $key): void
    {
        if (!$this->has($key)) {
            throw new RuntimeException("{$key} must be configured in metadata");
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
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->has($key) ? $this->metadata[$key] : $default;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->metadata);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->metadata;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->metadata;
    }

    /**
     * Return a clone object with new value
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function withMetadata(string $key, $value)
    {
        $clone = clone $this;
        $clone->metadata[$key] = $value;

        return $clone;
    }
}
