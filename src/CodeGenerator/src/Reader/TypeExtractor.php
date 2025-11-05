<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Reader;

use cebe\openapi\spec\Reference;
use cebe\openapi\spec\Schema as OpenApiSchema;
use P8p\CodeGenerator\Config\Config;
use P8p\CodeGenerator\Exception\ReaderException;
use P8p\CodeGenerator\Model\Model;
use Symfony\Component\TypeInfo\Type;

class TypeExtractor
{
    public function __construct(
        private readonly Config $config,
        private readonly ExternalTypeRegistry $externalTypeRegistry,
    ) {
    }

    public function extract(OpenApiSchema|Reference $spec, Model $model): Type
    {
        if ($spec instanceof Reference) {
            throw new ReaderException(sprintf('extract need a "%s" object', OpenApiSchema::class));
        }

        if ($spec->allOf) {
            return $this->extractAllOf($spec->allOf, $model);
        } elseif ($spec->oneOf) {
            return $this->extractUnion($spec->oneOf, $model);
        } elseif ($spec->anyOf) {
            return $this->extractUnion($spec->anyOf, $model);
        } elseif ($spec->items) {
            // Collection of items - use list() for arrays with indexed values
            $itemType = $this->extract($spec->items, $model);

            return Type::list($itemType);
        } elseif ('object' === $spec->type && $spec->additionalProperties instanceof OpenApiSchema) {
            // Map/dictionary with additional properties (array<string, ValueType>)
            return Type::array();
        } elseif ($ref = $this->extractClassName($spec, $model)) {
            return $ref;
        } elseif ($spec->type) {
            return $this->map($spec->type);
        }

        throw new ReaderException(sprintf('Unable to convert type for field "%s"', $spec->getDocumentPosition()?->getPointer()));
    }

    /**
     * Extract intersection type from allOf.
     *
     * @param array<OpenApiSchema|Reference> $specs
     */
    private function extractAllOf(array $specs, Model $model): Type
    {
        $types = [];
        foreach ($specs as $spec) {
            $types[] = $this->extract($spec, $model);
        }

        // If we have only one type, return it directly
        if (1 === count($types)) {
            return $types[0];
        }

        return Type::intersection(...$types);
    }

    /**
     * Extract union type from oneOf/anyOf.
     *
     * @param array<OpenApiSchema|Reference> $specs
     */
    private function extractUnion(array $specs, Model $model): Type
    {
        $types = [];
        foreach ($specs as $spec) {
            $types[] = $this->extract($spec, $model);
        }

        // If we have only one type, return it directly
        if (1 === count($types)) {
            return $types[0];
        }

        return Type::union(...$types);
    }

    public function extractClassName(OpenApiSchema $spec, Model $model): ?Type
    {
        $path = $spec->getDocumentPosition()?->getPath() ?? [];
        if (3 === count($path)) {
            $name = $path[2];

            if (isset($this->config->schemasOverride[$name])) {
                return $this->config->schemasOverride[$name];
            }

            $systemOverrides = self::getSystemOverrides();
            if (isset($systemOverrides[$name])) {
                return $systemOverrides[$name];
            }

            if ($this->externalTypeRegistry->hasSchema($name)) {
                $fqcn = $this->externalTypeRegistry->resolveSchemaName($name);

                return Type::object($fqcn);
            }

            if (!$model->hasSchema($name)) {
                throw new ReaderException(sprintf('Unable to map type. Missing schema "%s"', $name));
            }

            return Type::object($model->getSchema($name)->getClassMetadata()->name);
        }

        return null;
    }

    private function map(string $type): Type
    {
        return match ($type) {
            'string' => Type::string(),
            'array', 'object' => Type::array(),
            'integer' => Type::int(),
            'boolean' => Type::bool(),
            'number' => Type::float(),
            default => throw new ReaderException(sprintf('Unable to map type from "%s"', $type)),
        };
    }

    /**
     * Get system type overrides for special Kubernetes types.
     * These types have specific PHP representations that differ from their OpenAPI schema.
     *
     * @return array<string, Type>
     */
    private function getSystemOverrides(): array
    {
        return [
            'io.k8s.apimachinery.pkg.util.intstr.IntOrString' => Type::union(Type::int(), Type::string()),
            'io.k8s.apimachinery.pkg.api.resource.Quantity' => Type::union(Type::int(), Type::string()),
            'io.k8s.apimachinery.pkg.runtime.RawExtension' => Type::union(Type::array(), Type::object()),
            'io.k8s.apimachinery.pkg.apis.meta.v1.Time' => Type::object(\DateTime::class),
            'io.k8s.apimachinery.pkg.apis.meta.v1.MicroTime' => Type::object(\DateTime::class),
            'io.k8s.apimachinery.pkg.apis.meta.v1.FieldsV1' => Type::array(),
            'io.k8s.apimachinery.pkg.apis.meta.v1.Patch' => Type::array(),
            'io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.CustomResourceSubresourceStatus' => Type::array(),
            'io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.JSON' => Type::array(),
            'io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.JSONSchemaProps' => Type::array(),
            'io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.JSONSchemaPropsOrBool' => Type::union(Type::array(), Type::bool()),
            'io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.JSONSchemaPropsOrStringArray' => Type::array(),
            'io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.JSONSchemaPropsOrArray' => Type::array(),
        ];
    }

    public function isSystemOverride(string $schemaName): bool
    {
        return isset($this->getSystemOverrides()[$schemaName]);
    }
}
