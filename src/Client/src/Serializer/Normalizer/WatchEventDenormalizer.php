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

use P8p\Sdk\Schema\Meta\V1\WatchEvent;
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

        if (!isset($context[self::WATCH_OBJECT_CLASS]) || !is_string($context[self::WATCH_OBJECT_CLASS])) {
            throw new InvalidArgumentException('WATCH_OBJECT_CLASS context is required and must be a string');
        }

        return new WatchEvent( /* @phpstan-ignore class.notFound */
            object: $this->denormalizer->denormalize($data['object'], $context[self::WATCH_OBJECT_CLASS], $format, $context),
            type: $data['type'],
        );
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return WatchEvent::class === $type && isset($context[self::WATCH_OBJECT_CLASS]); /* @phpstan-ignore class.notFound */
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            WatchEvent::class => true, /* @phpstan-ignore class.notFound */
        ];
    }
}
