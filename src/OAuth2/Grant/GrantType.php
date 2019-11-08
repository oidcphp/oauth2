<?php

namespace OpenIDConnect\OAuth2\Grant;

use InvalidArgumentException;

/**
 * Represents a type of authorization grant.
 *
 * @see https://tools.ietf.org/html/rfc6749#section-1.3
 */
abstract class GrantType
{
    /**
     * @var string
     */
    protected $grantType;

    /**
     * @var array
     */
    protected $tokenRequestParameters = [];

    /**
     * Prepares the parameters used on token endpoint
     *
     * @param array $parameters
     * @return array
     */
    public function prepareTokenRequestParameters(array $parameters): array
    {
        // Check the parameters is ready
        foreach (array_merge($this->tokenRequestParameters) as $name) {
            if (!isset($parameters[$name])) {
                throw new InvalidArgumentException("Missing parameter '{$name}'");
            }
        }

        return array_merge([
            'grant_type' => $this->grantType,
        ], $parameters);
    }
}
