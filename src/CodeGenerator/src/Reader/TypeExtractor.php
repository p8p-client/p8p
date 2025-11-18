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
use P8p\CodeGenerator\Model\Property;
use Symfony\Component\TypeInfo\Type;

class TypeExtractor
{
    /**
     * @var array<string, Type> System type overrides for special Kubernetes types
     */
    private readonly array $systemOverrides;

    public function __construct(
        private readonly Config $config,
        private readonly ExternalTypeRegistry $externalTypeRegistry,
        private readonly InlineSchemaGenerator $inlineSchemaGenerator,
    ) {
        $this->systemOverrides = $this->initializeSystemOverrides();
    }

    public function extract(OpenApiSchema|Reference $spec, Model $model, string $group, string $version): Type
    {
        if ($spec instanceof Reference) {
            throw new ReaderException(sprintf('extract need a "%s" object', OpenApiSchema::class));
        }

        if ($spec->allOf) {
            return $this->extractAllOf($spec->allOf, $model, $group, $version);
        } elseif ($spec->oneOf) {
            return $this->extractUnion($spec->oneOf, $model, $group, $version);
        } elseif ($spec->anyOf) {
            return $this->extractUnion($spec->anyOf, $model, $group, $version);
        } elseif ($spec->items) {
            // Collection of items - use list() for arrays with indexed values
            $itemType = $this->extract($spec->items, $model, $group, $version);

            return Type::list($itemType);
        } elseif ('object' === $spec->type && $spec->additionalProperties instanceof OpenApiSchema) {
            // Map/dictionary with additional properties (array<string, ValueType>)
            return Type::array();
        } elseif ($ref = $this->extractClassName($spec, $model)) {
            return $ref;
        } elseif ($ref = $this->extractInlineSchemaClass($spec, $model, $group, $version)) {
            return $ref;
        } elseif ($spec->type) {
            return $this->map($spec->type);
        } elseif ($this->hasPreserveUnknownFields($spec)) {
            // Handle x-kubernetes-preserve-unknown-fields: arbitrary JSON data
            return Type::array();
        }

        throw new ReaderException(sprintf('Unable to convert type for field "%s"', $spec->getDocumentPosition()?->getPointer()));
    }

    /**
     * Extract intersection type from allOf.
     *
     * @param array<OpenApiSchema|Reference> $specs
     */
    private function extractAllOf(array $specs, Model $model, string $group, string $version): Type
    {
        $types = [];
        foreach ($specs as $spec) {
            $types[] = $this->extract($spec, $model, $group, $version);
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
    private function extractUnion(array $specs, Model $model, string $group, string $version): Type
    {
        $types = [];
        foreach ($specs as $spec) {
            $types[] = $this->extract($spec, $model, $group, $version);
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

            if (isset($this->systemOverrides[$name])) {
                return $this->systemOverrides[$name];
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

    /**
     * Extract class name for inline object schemas, generating them on-the-fly if needed.
     *
     * Handles objects defined inline (not via $ref) that have properties.
     * Example path: ['components', 'schemas', 'com.example.food.v1alpha1.Pizzeria', 'properties', 'spec']
     * Will generate synthetic name: 'com.example.food.x.Pizzeria.spec'
     */
    private function extractInlineSchemaClass(OpenApiSchema $spec, Model $model, string $group, string $version): ?Type
    {
        // Only process objects with properties (inline structured objects)
        if ('object' !== $spec->type || empty($spec->properties)) {
            return null;
        }

        $schemaPath = SchemaPath::fromDocumentPath($spec->getDocumentPosition()?->getPath() ?? []);
        if (!$schemaPath) {
            return null;
        }

        $syntheticName = $schemaPath->toSyntheticName();

        // Generate the schema on-the-fly if it doesn't exist yet
        if (!$model->hasSchema($syntheticName)) {
            // Generate all inline schemas recursively for this spec
            $inlineSchemas = $this->inlineSchemaGenerator->generate($spec, $syntheticName, $group, $version);

            // Add the main schema
            $schema = $this->createInlineSchema($spec, $syntheticName, $group, $version);
            $model->addSchema($schema);

            // Add all nested schemas
            foreach ($inlineSchemas as $nestedSchema) {
                if (!$model->hasSchema($nestedSchema->getName())) {
                    $model->addSchema($nestedSchema);
                }
            }

            // Resolve properties for the main schema
            $this->resolveSchemaProperties($spec, $schema, $model, $group, $version);

            // Resolve properties for nested schemas
            foreach ($inlineSchemas as $nestedSchema) {
                $nestedSpec = $this->findSchemaSpec($spec, $nestedSchema->getName(), $syntheticName);
                if ($nestedSpec) {
                    $this->resolveSchemaProperties($nestedSpec, $nestedSchema, $model, $group, $version);
                }
            }
        }

        return Type::object($model->getSchema($syntheticName)->getClassMetadata()->name);
    }

    /**
     * Create a Schema object for an inline object.
     */
    private function createInlineSchema(OpenApiSchema $spec, string $syntheticName, string $group, string $version): \P8p\CodeGenerator\Model\Schema
    {
        $schema = new \P8p\CodeGenerator\Model\Schema($syntheticName);
        $schema->setDescription($spec->description);

        $classMetadata = $this->inlineSchemaGenerator->getClassMetadataExtractor()->extractForSyntheticSchema($syntheticName, $group, $version);
        $schema->setClassMetadata($classMetadata);

        return $schema;
    }

    /**
     * Resolve properties for a schema.
     */
    private function resolveSchemaProperties(OpenApiSchema $spec, \P8p\CodeGenerator\Model\Schema $schema, Model $model, string $group, string $version): void
    {
        foreach ($spec->properties as $propertyName => $propertySpec) {
            // Skip references - they will be handled by extract()
            if ($propertySpec instanceof Reference) {
                continue;
            }

            $type = $this->extract($propertySpec, $model, $group, $version);
            if (!in_array($propertyName, $spec->required ?? [])) {
                $type = Type::nullable($type);
            }

            $property = new Property($propertyName, $propertySpec->description, $type);
            $schema->addProperty($property);
        }

        $schema->reorderProperties();
    }

    /**
     * Find the SchemaSpec for a nested inline schema.
     */
    private function findSchemaSpec(OpenApiSchema $parentSpec, string $nestedSchemaName, string $parentName): ?OpenApiSchema
    {
        // Extract the property path from the nested schema name
        // Example: parentName = 'com.example.food.v1alpha1.Pizzeria.spec'
        //          nestedSchemaName = 'com.example.food.v1alpha1.Pizzeria.spec.chef'
        //          propertyPath = ['chef']

        if (!str_starts_with($nestedSchemaName, $parentName.'.')) {
            return null;
        }

        $relativePath = substr($nestedSchemaName, strlen($parentName) + 1);
        $propertyPath = explode('.', $relativePath);

        $currentSpec = $parentSpec;
        foreach ($propertyPath as $propertyName) {
            if (!isset($currentSpec->properties[$propertyName])) {
                return null;
            }

            $propertySpec = $currentSpec->properties[$propertyName];

            // Skip references
            if ($propertySpec instanceof Reference) {
                return null;
            }

            // Handle array items
            if ('array' === $propertySpec->type && $propertySpec->items) {
                $itemSpec = $propertySpec->items;
                if ($itemSpec instanceof Reference) {
                    return null;
                }
                $currentSpec = $itemSpec;
            } else {
                $currentSpec = $propertySpec;
            }

            if ('object' !== $currentSpec->type) {
                return null;
            }
        }

        return $currentSpec;
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
     * Initialize system type overrides for special Kubernetes types.
     * These types have specific PHP representations that differ from their OpenAPI schema.
     *
     * @return array<string, Type>
     */
    private function initializeSystemOverrides(): array
    {
        return [
            'io.k8s.apimachinery.pkg.util.intstr.IntOrString' => Type::union(Type::int(), Type::string()),
            'io.k8s.apimachinery.pkg.api.resource.Quantity' => Type::union(Type::int(), Type::string()),
            'io.k8s.apimachinery.pkg.runtime.RawExtension' => Type::union(Type::array(), Type::object()),
            'io.k8s.apimachinery.pkg.apis.meta.v1.Time' => Type::object(\DateTime::class),
            'io.k8s.apimachinery.pkg.apis.meta.v1.MicroTime' => Type::object(\DateTime::class),
            'io.k8s.apimachinery.pkg.apis.meta.v1.FieldsV1' => Type::array(),
            'io.k8s.apimachinery.pkg.apis.meta.v1.Patch' => Type::array(),
            'io.k8s.apimachinery.pkg.apis.meta.v1.Duration' => Type::string(),
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
        return isset($this->systemOverrides[$schemaName]);
    }

    /**
     * Check if the schema has x-kubernetes-preserve-unknown-fields extension.
     */
    private function hasPreserveUnknownFields(OpenApiSchema $spec): bool
    {
        $extensions = $spec->getExtensions();

        return isset($extensions['x-kubernetes-preserve-unknown-fields']) && true === $extensions['x-kubernetes-preserve-unknown-fields'];
    }
}
