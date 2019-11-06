<?php

declare(strict_types=1);

namespace OpenIDConnect\OAuth2\Builder;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class AuthorizationFormResponseBuilder
{
    use BuilderTrait;

    public function build(array $parameters): ResponseInterface
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->container->get(ResponseFactoryInterface::class);

        /** @var StreamFactoryInterface $streamFactory */
        $streamFactory = $this->container->get(StreamFactoryInterface::class);

        return $responseFactory->createResponse()
            ->withBody($streamFactory->createStream($this->generateForm($parameters)));
    }

    /**
     * @param array $parameters
     * @return string
     */
    private function generateForm(array $parameters): string
    {
        return $this->generateHtml(
            $this->providerMetadata->authorizationEndpoint(),
            $parameters
        );
    }

    /**
     * @param string $baseAuthorizationUrl
     * @param array $parameters
     * @return string
     */
    private function generateHtml(string $baseAuthorizationUrl, array $parameters): string
    {
        $formInput = implode('', array_map(function ($v, $k) {
            return "<input type=\"hidden\" name=\"${k}\" value=\"${v}\"/>";
        }, $parameters, array_keys($parameters)));

        return <<< HTML
<!DOCTYPE html>
<head><title>Requesting Authorization</title></head>
<body onload="javascript:document.forms[0].submit()">
<form method="post" action="{$baseAuthorizationUrl}">{$formInput}</form>
</body>
</html>
HTML;
    }
}
