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

use P8p\Client\Serializer\Normalizer\WatchEventDenormalizer;
use P8p\Client\Tests\Fixtures\Schema\SimplePod;
use P8p\Client\Tests\Fixtures\WatchEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class WatchEventDenormalizerTest extends TestCase
{
    private WatchEventDenormalizer $denormalizer;
    /** @var DenormalizerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private DenormalizerInterface $mockDenormalizer;

    protected function setUp(): void
    {
        if (!class_exists('P8p\Sdk\Schema\Core\V1\WatchEvent')) {
            class_alias(WatchEvent::class, 'P8p\Sdk\Schema\Core\V1\WatchEvent');
        }

        $this->denormalizer = new WatchEventDenormalizer();
        $this->mockDenormalizer = $this->createMock(DenormalizerInterface::class);
        $this->denormalizer->setDenormalizer($this->mockDenormalizer);
    }

    public function testDenormalizeCallsSubDenormalizerWithCorrectParameters(): void
    {
        $podData = ['name' => 'test-pod', 'namespace' => 'default'];
        $data = [
            'type' => 'ADDED',
            'object' => $podData,
        ];
        $context = [WatchEventDenormalizer::WATCH_OBJECT_CLASS => SimplePod::class];

        $expectedPod = new SimplePod('test-pod', 'default');

        $this->mockDenormalizer->expects($this->once())
            ->method('denormalize')
            ->with($podData, SimplePod::class, null, $context)
            ->willReturn($expectedPod);

        $this->denormalizer->denormalize($data, 'P8p\Sdk\Schema\Core\V1\WatchEvent', null, $context);
    }

    public function testDenormalizeExtractsCorrectDataFromArray(): void
    {
        $objectData = ['name' => 'test-pod'];
        $data = [
            'type' => 'MODIFIED',
            'object' => $objectData,
        ];
        $context = [WatchEventDenormalizer::WATCH_OBJECT_CLASS => SimplePod::class];

        $expectedPod = new SimplePod('test-pod', 'default');

        $this->mockDenormalizer->expects($this->once())
            ->method('denormalize')
            ->with($objectData, SimplePod::class, null, $context)
            ->willReturn($expectedPod);

        $this->denormalizer->denormalize($data, 'P8p\Sdk\Schema\Core\V1\WatchEvent', null, $context);
    }

    public function testDenormalizeThrowsExceptionForNonArrayData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data must be an array');

        $context = [WatchEventDenormalizer::WATCH_OBJECT_CLASS => SimplePod::class];

        $this->denormalizer->denormalize('not an array', 'P8p\Sdk\Schema\Core\V1\WatchEvent', null, $context);
    }

    public function testDenormalizeThrowsExceptionWhenContextMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('WATCH_OBJECT_CLASS context is required and must be a string');

        $data = [
            'type' => 'ADDED',
            'object' => ['name' => 'test-pod'],
        ];

        $this->denormalizer->denormalize($data, 'P8p\Sdk\Schema\Core\V1\WatchEvent', null, []);
    }

    public function testDenormalizeThrowsExceptionWhenContextNotString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('WATCH_OBJECT_CLASS context is required and must be a string');

        $data = [
            'type' => 'ADDED',
            'object' => ['name' => 'test-pod'],
        ];
        $context = [WatchEventDenormalizer::WATCH_OBJECT_CLASS => 123];

        $this->denormalizer->denormalize($data, 'P8p\Sdk\Schema\Core\V1\WatchEvent', null, $context);
    }

    public function testSupportsDenormalizationReturnsTrueForWatchEventWithContext(): void
    {
        $context = [WatchEventDenormalizer::WATCH_OBJECT_CLASS => SimplePod::class];

        $result = $this->denormalizer->supportsDenormalization([], 'P8p\Sdk\Schema\Core\V1\WatchEvent', null, $context);

        $this->assertTrue($result);
    }

    public function testGetSupportedTypes(): void
    {
        $result = $this->denormalizer->getSupportedTypes(null);

        $this->assertArrayHasKey('P8p\Sdk\Schema\Core\V1\WatchEvent', $result);
        $this->assertTrue($result['P8p\Sdk\Schema\Core\V1\WatchEvent']);
    }
}
