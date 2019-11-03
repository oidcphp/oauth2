<?php

namespace OpenIDConnect\OAuth2\ClientAuthentication;

use OpenIDConnect\Support\Http\Query;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\stream_for;

/**
 * @see https://tools.ietf.org/html/rfc6749#section-2.3.1
 */
class ClientSecretPost extends ClientAuthentication
{
    /**
     * @var string
     */
    private $client;

    /**
     * @var string
     */
    private $secret;

    /**
     * @param string $client
     * @param string $secret
     */
    public function __construct(string $client, string $secret)
    {
        $this->client = $client;
        $this->secret = $secret;
    }

    /**
     * @inheritDoc
     */
    protected function processRequest(RequestInterface $request): RequestInterface
    {
        $body = (string)$request->getBody();

        $parsedBody = Query::parse($body);
        $parsedBody['client_id'] = $this->client;
        $parsedBody['client_secret'] = $this->secret;

        return $request->withBody(stream_for(Query::build($parsedBody)));
    }
}
