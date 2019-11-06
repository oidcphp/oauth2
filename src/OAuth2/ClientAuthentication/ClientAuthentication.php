<?php

declare(strict_types=1);

namespace OpenIDConnect\OAuth2\ClientAuthentication;

use LogicException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

interface ClientAuthentication
{
    /**
     * Process authentication message in the request
     *
     * @param RequestInterface $request
     * @return RequestInterface
     */
    public function processRequest(RequestInterface $request): RequestInterface;
}
