<?php

declare(strict_types=1);

namespace OpenIDConnect\OAuth2\ClientAuthentication;

use LogicException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

abstract class ClientAuthentication implements RequestFactoryInterface
{
    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @inheritDoc
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        if (null === $this->requestFactory) {
            throw new LogicException('Missing the base request factory');
        }

        $request = $this->requestFactory->createRequest($method, $uri);

        return $this->processRequest($request);
    }

    /**
     * @param RequestFactoryInterface $requestFactory
     * @return ClientAuthentication
     */
    public function setRequestFactory(RequestFactoryInterface $requestFactory): ClientAuthentication
    {
        $this->requestFactory = $requestFactory;

        return $this;
    }

    /**
     * Process authentication message in the request
     *
     * @param RequestInterface $request
     * @return RequestInterface
     */
    abstract protected function processRequest(RequestInterface $request): RequestInterface;
}
