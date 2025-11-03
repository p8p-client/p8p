<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\CustomResource;

use P8p\Client\Attribute\K8sCustomResourceSchema;
use P8p\Client\Exception\CrdException;
use P8p\Sdk\Schema\Apiextensions\V1\CustomResourceDefinition;
use P8p\Sdk\Schema\Apiextensions\V1\CustomResourceDefinitionNames;
use P8p\Sdk\Schema\Apiextensions\V1\CustomResourceDefinitionSpec;
use P8p\Sdk\Schema\Apiextensions\V1\CustomResourceDefinitionVersion;
use P8p\Sdk\Schema\Apiextensions\V1\CustomResourceValidation;
use P8p\Sdk\Schema\Meta\V1\ObjectMeta;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\BackedEnumType;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\Type\UnionType;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolver;

class CustomResourceDefinitionBuilder
{
    /**
     * Build a CustomResourceDefinition from one or more resource classes.
     *
     * When multiple classes are provided, they must share the same group/kind/names
     * but can have different versions. The last version in the array will be marked
     * as the storage version.
     *
     * @param string|array<string> $resourceClasses Single class or array of versioned classes
     */
    public function build(string|array $resourceClasses): CustomResourceDefinition /* @phpstan-ignore class.notFound */
    {
        $resourceClasses = is_array($resourceClasses) ? $resourceClasses : [$resourceClasses];

        if (empty($resourceClasses)) {
            throw new CrdException('At least one resource class must be provided');
        }

        // Extract metadata from all classes and validate they're compatible
        $allMetadata = array_map(fn (string $class) => $this->extractMetadata($class), $resourceClasses); /* @phpstan-ignore argument.type */
        $firstMetadata = $allMetadata[0];

        // Validate that all versions share the same group/kind/names
        foreach ($allMetadata as $metadata) {
            if ($metadata->group !== $firstMetadata->group
                || $metadata->kind !== $firstMetadata->kind
                || $metadata->plural !== $firstMetadata->plural) {
                throw new CrdException('All resource classes must have the same group, kind, and plural name');
            }
        }

        // Generate versions - mark only the last one as storage
        $versions = [];
        foreach ($resourceClasses as $index => $resourceClass) {
            $metadata = $allMetadata[$index];
            $schema = $this->generateOpenApiSchema($resourceClass); /* @phpstan-ignore argument.type */
            $isLastVersion = ($index === count($resourceClasses) - 1);

            $versions[] = new CustomResourceDefinitionVersion( /* @phpstan-ignore class.notFound */
                name: $metadata->version,
                served: true,
                storage: $isLastVersion, // Only the last version is the storage version
                schema: new CustomResourceValidation( /* @phpstan-ignore class.notFound */
                    openAPIV3Schema: $schema,
                ),
            );
        }

        return new CustomResourceDefinition( /* @phpstan-ignore class.notFound */
            spec: new CustomResourceDefinitionSpec( /* @phpstan-ignore class.notFound */
                group: $firstMetadata->group,
                names: new CustomResourceDefinitionNames( /* @phpstan-ignore class.notFound */
                    kind: $firstMetadata->kind,
                    plural: $firstMetadata->plural,
                    shortNames: [$firstMetadata->shortName],
                    singular: $firstMetadata->singular,
                ),
                scope: $firstMetadata->getScope(),
                versions: $versions,
            ),
            metadata: new ObjectMeta( /* @phpstan-ignore class.notFound */
                name: $firstMetadata->getName(),
            )
        );
    }

    /**
     * Extract K8sCustomResourceSchema metadata from a class.
     *
     * @template T of object
     *
     * @param class-string<T> $resourceClass
     *
     * @throws CrdException If the class doesn't have a K8sCustomResourceSchema attribute
     */
    private function extractMetadata(string $resourceClass): K8sCustomResourceSchema
    {
        $reflection = new \ReflectionClass($resourceClass);
        $attributes = $reflection->getAttributes(K8sCustomResourceSchema::class);

        if (empty($attributes)) {
            throw new CrdException(sprintf('Class %s must have a #[K8sCustomResourceSchema] attribute', $resourceClass));
        }

        return $attributes[0]->newInstance();
    }

    /**
     * Generate OpenAPI v3 schema from a PHP class.
     *
     * @param class-string $className
     *
     * @return array<string, mixed>
     */
    private function generateOpenApiSchema(string $className): array
    {
        $reflection = new \ReflectionClass($className);
        $resolver = TypeResolver::create();

        $properties = [];
        $required = [];

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();

            // Skip standard Kubernetes
            if (in_array($propertyName, ['apiVersion', 'kind', 'metadata'], true)) {
                continue;
            }

            $type = $resolver->resolve($property);
            $propertySchema = $this->convertTypeToOpenApi($type);

            $properties[$propertyName] = $propertySchema;

            if (!$type->isNullable() && !$property->hasDefaultValue()) {
                $required[] = $propertyName;
            }
        }

        $schema = [
            'type' => 'object',
            'properties' => $properties,
        ];

        if (!empty($required)) {
            $schema['required'] = $required;
        }

        return $schema;
    }

    /**
     * Convert a Symfony TypeInfo Type to OpenAPI schema.
     *
     * @return array<string, mixed>
     */
    private function convertTypeToOpenApi(Type $type): array
    {
        // Handle nullable types (which are actually UnionTypes with null)
        if ($type->isNullable() && $type instanceof UnionType) {
            $types = $type->getTypes();
            // Filter out null type (BuiltinType with NULL identifier)
            $nonNullTypes = array_filter($types, function (Type $t) {
                if ($t instanceof BuiltinType) {
                    return 'null' !== $t->getTypeIdentifier()->value;
                }

                return true;
            });

            if (1 === count($nonNullTypes)) {
                // Simple nullable type like ?string
                return $this->convertTypeToOpenApi(array_values($nonNullTypes)[0]);
            }

            // Complex union with null - handle as oneOf
            $schemas = array_map(fn (Type $t) => $this->convertTypeToOpenApi($t), $nonNullTypes);

            return ['oneOf' => $schemas];
        }

        // Handle union types (non-nullable)
        if ($type instanceof UnionType) {
            $types = $type->getTypes();
            $schemas = array_map(fn (Type $t) => $this->convertTypeToOpenApi($t), $types);

            return ['oneOf' => $schemas];
        }

        // Handle collection types (arrays/lists)
        if ($type instanceof CollectionType) {
            $valueType = $type->getCollectionValueType();
            $itemSchema = $this->convertTypeToOpenApi($valueType);

            return [
                'type' => 'array',
                'items' => $itemSchema,
            ];
        }

        // Handle builtin types
        if ($type instanceof BuiltinType) {
            return $this->convertBuiltinTypeToOpenApi($type);
        }

        // Handle enum types
        if ($type instanceof BackedEnumType) {
            $enumClass = $type->getClassName();
            $cases = array_map(fn (\BackedEnum $case) => $case->value, $enumClass::cases());

            return [
                'type' => 'string',
                'enum' => $cases,
            ];
        }

        // Handle object types (custom classes)
        if ($type instanceof ObjectType) {
            return $this->generateOpenApiSchema($type->getClassName()); /* @phpstan-ignore argument.type */
        }

        throw new CrdException(sprintf('Unsupported type %s', $type));
    }

    /**
     * Convert a builtin type to OpenAPI schema.
     *
     * @param BuiltinType<\Symfony\Component\TypeInfo\TypeIdentifier> $type
     *
     * @return array<string, mixed>
     */
    private function convertBuiltinTypeToOpenApi(BuiltinType $type): array
    {
        $typeId = $type->getTypeIdentifier();

        return match ($typeId->value) {
            'string' => ['type' => 'string'],
            'int' => ['type' => 'integer', 'format' => 'int32'],
            'float' => ['type' => 'number', 'format' => 'double'],
            'bool' => ['type' => 'boolean'],
            'mixed' => ['type' => 'object'],
            default => throw new CrdException(sprintf('Unsupported type %s', $type->getTypeIdentifier()->value)),
        };
    }
}
