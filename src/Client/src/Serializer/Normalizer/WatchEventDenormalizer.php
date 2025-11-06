<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Serializer\Normalizer;

use P8p\Client\Exception\MissingDependencyException;
use P8p\Sdk\Schema\Core\V1\WatchEvent;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class WatchEventDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public const string WATCH_OBJECT_CLASS = 'p8p.watch_event.object.class';

    /**
     * @param array<string, mixed> $context
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException('Data must be an array');
        }

        if (!is_string($data['type'])) {
            throw new InvalidArgumentException('Data must have a "type" key with a string value');
        }

        if (!isset($context[self::WATCH_OBJECT_CLASS]) || !is_string($context[self::WATCH_OBJECT_CLASS])) {
            throw new InvalidArgumentException('WATCH_OBJECT_CLASS context is required and must be a string');
        }

        if (!class_exists(WatchEvent::class)) {
            throw MissingDependencyException::forPackage(package: 'p8p/sdk', feature: 'WatchEventDenormalizer::denormalize');
        }

        /** @var array<mixed>|object $object */
        $object = $this->denormalizer->denormalize($data['object'], $context[self::WATCH_OBJECT_CLASS], $format, $context);

        return new WatchEvent(
            object: $object,
            type: $data['type'],
        );
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return WatchEvent::class === $type && isset($context[self::WATCH_OBJECT_CLASS]);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            WatchEvent::class => true,
        ];
    }
}
