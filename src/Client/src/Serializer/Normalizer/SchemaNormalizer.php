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

use P8p\Client\Attribute\K8sSchema;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SchemaNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    private const string ALREADY_CALLED = 'p8p.schema_normalizer.already.called';

    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $context[self::ALREADY_CALLED] = true;

        if (!is_object($data)) {
            throw new InvalidArgumentException();
        }

        $attribute = $this->getSchemaAttribute($data);

        if (!$attribute) {
            throw new InvalidArgumentException();
        }

        /** @var array<string, mixed> $normalized */
        $normalized = $this->normalizer->normalize($data, $format, $context);

        return array_merge([
            'kind' => $attribute->kind,
            'apiVersion' => $attribute->getApiVersion(),
        ], $normalized);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return is_object($data) && null !== $this->getSchemaAttribute($data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            'object' => false,
        ];
    }

    private function getSchemaAttribute(object $object): ?K8sSchema
    {
        $ref = new \ReflectionClass($object);
        $attributes = $ref->getAttributes(K8sSchema::class);

        foreach ($attributes as $attribute) {
            return $attribute->newInstance();
        }

        return null;
    }
}
