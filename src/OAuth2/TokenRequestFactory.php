<?php

namespace OpenIDConnect\OAuth2;

use OpenIDConnect\Support\Http\Query;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\stream_for;

/**
 * Generate Request for token endpoint
 *
 * @see https://tools.ietf.org/html/rfc6749#section-3.2
 */
class TokenRequestFactory implements RequestFactoryInterface
{
    /**
     * @var RequestFactoryInterface
     */
    private $baseFactory;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param RequestFactoryInterface $baseFactory
     * @param array $parameters
     */
    public function __construct(RequestFactoryInterface $baseFactory, array $parameters)
    {
        $this->baseFactory = $baseFactory;
        $this->parameters = $parameters;
    }

    public function createRequest(string $method, $uri): RequestInterface
    {
        return ($this->baseFactory->createRequest($method, $uri))
            ->withHeader('content-type', 'application/x-www-form-urlencoded')
            ->withBody(stream_for(Query::build($this->parameters)));
    }
}
