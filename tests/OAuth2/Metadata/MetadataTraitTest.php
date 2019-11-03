<?php

namespace Tests\OAuth2\Metadata;

use OpenIDConnect\OAuth2\Metadata\MetadataTraits;
use RuntimeException;
use Tests\TestCase;

class MetadataTraitTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnCorrectValueWhenCallHas(): void
    {
        /** @var MetadataTraits $target */
        $target = $this->getMockForTrait(MetadataTraits::class);

        $actual = $target->withMetadata('some', 'value');

        $this->assertTrue($actual->has('some'));
        $this->assertFalse($actual->has('whatever'));
    }

    /**
     * @test
     */
    public function shouldReturnDefaultValueWhenGet(): void
    {
        /** @var MetadataTraits $target */
        $target = $this->getMockForTrait(MetadataTraits::class);

        $this->assertSame('d', $target->get('some', 'd'));
    }

    /**
     * @test
     */
    public function shouldReturnNewObjectWhenUsingWithMetadata(): void
    {
        /** @var MetadataTraits $target */
        $target = $this->getMockForTrait(MetadataTraits::class);

        $actual = $target->withMetadata('some', 'value');

        $this->assertNotSame($target, $actual);
        $this->assertSame('value', $actual->get('some'));
    }

    /**
     * @test
     */
    public function shouldReturnArrayWhenCallToArray(): void
    {
        /** @var MetadataTraits $target */
        $target = $this->getMockForTrait(MetadataTraits::class);

        $actual = $target->withMetadata('foo', 'a')
            ->withMetadata('bar', 'z');

        $expected = [
            'foo' => 'a',
            'bar' => 'z',
        ];

        $this->assertSame($expected, $actual->jsonSerialize());
        $this->assertSame($expected, $actual->toArray());
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenAssertWithoutKey(): void
    {
        $this->expectException(RuntimeException::class);

        /** @var MetadataTraits $target */
        $target = $this->getMockForTrait(MetadataTraits::class);

        $target->assertHasKey('not-exist');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenAssertWithoutKeys(): void
    {
        $this->expectException(RuntimeException::class);

        /** @var MetadataTraits $target */
        $target = $this->getMockForTrait(MetadataTraits::class);

        $actual = $target->withMetadata('foo', 'bar');

        $actual->assertHasKeys(['foo', 'not-exist']);
    }
}
