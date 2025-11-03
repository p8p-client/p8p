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

use P8p\Client\CustomResource\CustomRessourceList;
use P8p\Sdk\Schema\Meta\V1\ListMeta;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CustomRessourceListDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    /**
     * @param array<string, mixed> $context
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException('Data must be an array');
        }

        $itemClass = $this->extractItemClass($type);

        if (!$itemClass) {
            throw new InvalidArgumentException(sprintf('Could not extract item class from type "%s"', $type));
        }

        $items = [];
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $items[] = $this->denormalizer->denormalize($item, $itemClass, $format, $context);
            }
        }

        $metadata = null;
        if (isset($data['metadata']) && class_exists(ListMeta::class)) {
            /** @var ListMeta $metadata */
            $metadata = $this->denormalizer->denormalize($data['metadata'], ListMeta::class, $format, $context);
        }

        return new CustomRessourceList($items, $metadata);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return str_starts_with($type, CustomRessourceList::class.'<');
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => false,
        ];
    }

    /**
     * Extract the item class from a generic type string like "CustomRessourceList<ItemClass>".
     */
    private function extractItemClass(string $type): ?string
    {
        if (preg_match('/^'.preg_quote(CustomRessourceList::class, '/').'<(.+)>$/', $type, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
