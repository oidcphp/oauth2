<?php

namespace Tests\OAuth2\Builder;

use OpenIDConnect\OAuth2\Builder\AuthorizationFormResponseBuilder;
use Tests\TestCase;

class AuthorizationFormResponseBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnAuthorizationHtmlWhenCallBuild(): void
    {
        $target = new AuthorizationFormResponseBuilder($this->createContainer());

        $actual = (string)$target->setProviderMetadata($this->createProviderMetadata())
            ->setClientInformation($this->createClientInformation())
            ->build([
                'foo' => 'a',
                'bar' => 'b',
            ])
            ->getBody();

        $this->assertStringContainsStringIgnoringCase('action="https://somewhere/authorization"', $actual);
        $this->assertStringContainsStringIgnoringCase('name="foo" value="a"', $actual);
        $this->assertStringContainsStringIgnoringCase('name="bar" value="b"', $actual);
    }
}
