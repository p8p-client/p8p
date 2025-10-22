<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\tests\Serializer\Normalizer;

use P8p\Client\Serializer\Normalizer\SchemaNormalizer;
use P8p\Client\Tests\Fixtures\Schema\TestClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SchemaNormalizerTest extends TestCase
{
    private SchemaNormalizer $normalizer;
    /** @var NormalizerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private NormalizerInterface $mockNormalizer;

    protected function setUp(): void
    {
        $this->normalizer = new SchemaNormalizer();
        $this->mockNormalizer = $this->createMock(NormalizerInterface::class);
        $this->normalizer->setNormalizer($this->mockNormalizer);
    }

    public function testNormalizeAddsKindAndApiVersion(): void
    {
        $object = new TestClass();

        $this->mockNormalizer->expects($this->once())
            ->method('normalize')
            ->with($object, null, $this->callback(function ($context) {
                return isset($context['p8p.schema_normalizer.already.called']);
            }))
            ->willReturn(['metadata' => ['name' => 'test']]);

        $result = $this->normalizer->normalize($object);

        $this->assertArrayHasKey('kind', $result);
        $this->assertArrayHasKey('apiVersion', $result);
        $this->assertSame('Deployment', $result['kind']);
        $this->assertSame('apps/v1', $result['apiVersion']);
        $this->assertArrayHasKey('metadata', $result);
        $this->assertSame(['name' => 'test'], $result['metadata']);
    }

    public function testNormalizeThrowsExceptionForNonObject(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->normalizer->normalize('not an object');
    }

    public function testNormalizeThrowsExceptionForObjectWithoutAttribute(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $objectWithoutAttribute = new \stdClass();
        $this->normalizer->normalize($objectWithoutAttribute);
    }

    public function testSupportsNormalizationReturnsTrueForObjectWithAttribute(): void
    {
        $object = new TestClass();

        $result = $this->normalizer->supportsNormalization($object);

        $this->assertTrue($result);
    }

    public function testSupportsNormalizationReturnsFalseForObjectWithoutAttribute(): void
    {
        $objectWithoutAttribute = new \stdClass();

        $result = $this->normalizer->supportsNormalization($objectWithoutAttribute);

        $this->assertFalse($result);
    }

    public function testSupportsNormalizationReturnsFalseForNonObject(): void
    {
        $result = $this->normalizer->supportsNormalization('not an object');

        $this->assertFalse($result);
    }

    public function testSupportsNormalizationReturnsFalseWhenAlreadyCalled(): void
    {
        $object = new TestClass();
        $context = ['p8p.schema_normalizer.already.called' => true];

        $result = $this->normalizer->supportsNormalization($object, null, $context);

        $this->assertFalse($result);
    }

    public function testGetSupportedTypes(): void
    {
        $result = $this->normalizer->getSupportedTypes(null);

        $this->assertArrayHasKey('object', $result);
        $this->assertFalse($result['object']);
    }
}
