<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Serializer;

use P8p\Client\Serializer\Normalizer\SchemaNormalizer;
use P8p\Client\Serializer\Normalizer\WatchEventDenormalizer;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class K8sSerializer extends Serializer
{
    public function __construct(array $normalizers = [], array $encoders = [], array $defaultContext = [])
    {
        parent::__construct($normalizers, $encoders, $defaultContext);
    }

    public static function createDefault(): self
    {
        $propertyInfo = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());

        $objectNormalizer = new ObjectNormalizer($classMetadataFactory, null, null, $propertyInfo);

        $normalizers = [
            new DateTimeNormalizer(),
            new SchemaNormalizer(),
            new WatchEventDenormalizer(),
            $objectNormalizer,
            new ArrayDenormalizer(),
        ];

        return new self($normalizers, [new JsonEncoder()]);
    }
}
