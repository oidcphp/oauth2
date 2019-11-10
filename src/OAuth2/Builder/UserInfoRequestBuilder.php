<?php declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Builder;

use BadMethodCallException;
use OpenIDConnect\OAuth2\Exceptions\OAuth2ServerException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Generate Request for userinfo endpoint
 *
 * @see https://openid.net/specs/openid-connect-core-1_0.html#UserInfoRequest
 */
class UserInfoRequestBuilder
{
    use BuilderTrait;

    public function build(string $accessToken): RequestInterface
    {
        /** @var RequestFactoryInterface $requestFactory */
        $requestFactory = $this->container->get(RequestFactoryInterface::class);

        try {
            $userInfoEndpoint = $this->providerMetadata->userinfoEndpoint();
        } catch (BadMethodCallException $e) {
            throw new OAuth2ServerException('Provider does not support userinfo_endpoint');
        }

        return $requestFactory->createRequest('GET', $userInfoEndpoint)
            ->withHeader('Authorization', 'Bearer ' . $accessToken);
    }
}
