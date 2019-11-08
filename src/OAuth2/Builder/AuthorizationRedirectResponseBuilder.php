<?php declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Builder;

use OpenIDConnect\Support\Http\Query;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * @see https://tools.ietf.org/html/rfc6749#section-4.1.1
 * @see https://tools.ietf.org/html/rfc6749#section-4.2.1
 */
class AuthorizationRedirectResponseBuilder
{
    use BuilderTrait;

    public function build(array $parameters): ResponseInterface
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->container->get(ResponseFactoryInterface::class);

        return $responseFactory->createResponse(302)
            ->withHeader('Location', (string)$this->createAuthorizeUri($parameters));
    }

    private function createAuthorizeUri(array $parameters)
    {
        /** @var UriFactoryInterface $uriFactory */
        $uriFactory = $this->container->get(UriFactoryInterface::class);

        return $uriFactory->createUri($this->providerMetadata->authorizationEndpoint())
            ->withQuery(Query::build($parameters));
    }
}
