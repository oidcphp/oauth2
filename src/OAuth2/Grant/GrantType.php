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
     * Return grant type name.
     *
     * @return string
     */
    abstract public function grantType(): string;

    /**
     * Returns a list of all required request parameters.
     *
     * @return array
     */
    abstract protected function requiredParameters(): array;

    /**
     * Check the request parameters
     *
     * @param array $parameters
     */
    private function checkRequestParameters(array $parameters): void
    {
        $required = $this->requiredParameters();
        $required[] = 'redirect_uri';

        foreach ($required as $name) {
            if (!isset($parameters[$name])) {
                throw new InvalidArgumentException("Missing parameter '{$name}'");
            }
        }
    }

    /**
     * Prepares an access token request's parameters.
     *
     * @param array $parameters
     * @return array
     */
    public function prepareRequestParameters(array $parameters): array
    {
        $this->checkRequestParameters($parameters);

        return array_merge([
            'grant_type' => $this->grantType(),
        ], $parameters);
    }
}
