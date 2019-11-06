<?php

declare(strict_types=1);

namespace OpenIDConnect\OAuth2;

use OpenIDConnect\OAuth2\Builder\AuthorizationFormResponseBuilder;
use OpenIDConnect\OAuth2\ClientAuthentication\ClientAuthenticationAwareTrait;
use OpenIDConnect\OAuth2\Metadata\ClientInformation;
use OpenIDConnect\OAuth2\Metadata\ClientInformationAwaitTrait;
use OpenIDConnect\OAuth2\Metadata\ProviderMetadata;
use OpenIDConnect\OAuth2\Metadata\ProviderMetadataAwaitTrait;
use OpenIDConnect\Support\Container\EntryNotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * OpenID Connect Client
 */
class Client
{
    use ClientAuthenticationAwareTrait;
    use ClientInformationAwaitTrait;
    use ProviderMetadataAwaitTrait;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var null|string
     */
    private $state;

    /**
     * @param ProviderMetadata $providerMetadata
     * @param ClientInformation $clientRegistration
     * @param ContainerInterface $container The container implements PSR-11
     */
    public function __construct(
        ProviderMetadata $providerMetadata,
        ClientInformation $clientRegistration,
        ContainerInterface $container
    ) {
        $this->setProviderMetadata($providerMetadata);
        $this->setClientInformation($clientRegistration);
        $this->setContainer($container);
    }

    /**
     * Create PSR-7 response with form post
     *
     * @param array $parameters
     * @return ResponseInterface
     */
    public function createAuthorizeFormPostResponse(array $parameters = []): ResponseInterface
    {
        return (new AuthorizationFormResponseBuilder($this->container))
            ->setClientInformation($this->clientInformation)
            ->setProviderMetadata($this->providerMetadata)
            ->build($this->generateAuthorizationParameters($parameters));
    }

    /**
     * @return null|string
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param ContainerInterface $container
     * @return Client
     */
    public function setContainer(ContainerInterface $container): Client
    {
        $entries = [
            StreamFactoryInterface::class,
            ResponseFactoryInterface::class,
            RequestFactoryInterface::class,
            UriFactoryInterface::class,
        ];

        foreach ($entries as $entry) {
            if (!$container->has($entry)) {
                throw new EntryNotFoundException("The entry '$entry' is not found");
            }
        }

        $this->container = $container;

        return $this;
    }

    /**
     * Initial the authorization parameters
     *
     * @param array $options
     */
    public function initAuthorizationParameters(array $options = []): void
    {
        if (!empty($options['state'])) {
            $this->state = $options['state'];
        }

        if (null === $this->state) {
            $this->state = $this->generateRandomString();
        }
    }

    /**
     * Generate the random string
     *
     * @param int $length
     * @return string
     */
    protected function generateRandomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function generateAuthorizationParameters(array $parameters): array
    {
        $this->initAuthorizationParameters($parameters);

        $parameters['state'] = $this->state;

        if (empty($parameters['scope'])) {
            $parameters['scope'] = ['openid'];
        }

        $parameters += [
            'response_type' => 'code',
        ];

        if (is_array($parameters['scope'])) {
            $parameters['scope'] = implode(' ', $parameters['scope']);
        }

        if (!isset($parameters['redirect_uri'])) {
            $parameters['redirect_uri'] = $this->clientInformation->redirectUri();
        }

        $parameters['client_id'] = $this->clientInformation->id();

        return $parameters;
    }
}
