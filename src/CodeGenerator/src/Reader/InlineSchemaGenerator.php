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
use cebe\openapi\spec\Schema as SchemaSpec;
use P8p\CodeGenerator\Model\ClassMetadata;
use P8p\CodeGenerator\Model\Schema;

/**
 * Generates synthetic Schema objects for inline object properties found in OpenAPI specs.
 *
 * This handles cases where CRDs define nested objects without creating separate schema references.
 * For example, a Pizzeria CRD might have an inline "spec" object with properties, rather than
 * a $ref to a separate schema. This generator creates synthetic schemas for these inline objects
 * so they can be generated as proper PHP classes.
 */
class InlineSchemaGenerator
{
    public function __construct(
        private readonly ClassMetadataExtractor $classMetadataExtractor,
    ) {
    }

    public function getClassMetadataExtractor(): ClassMetadataExtractor
    {
        return $this->classMetadataExtractor;
    }

    /**
     * Process a schema and generate synthetic schemas for all inline objects.
     *
     * @param SchemaSpec $schema           The parent schema to process
     * @param string     $parentSchemaName The OpenAPI schema name (e.g., 'com.example.food.v1alpha1.Pizzeria')
     * @param string     $group            The group from config
     * @param string     $version          The version from config
     *
     * @return array<Schema> Array of synthetic Schema objects for inline properties
     */
    public function generate(SchemaSpec $schema, string $parentSchemaName, string $group, string $version): array
    {
        $inlineSchemas = [];

        if (empty($schema->properties)) {
            return $inlineSchemas;
        }

        foreach ($schema->properties as $propertyName => $propertySpec) {
            // Skip references - they are handled by the normal schema resolution
            if ($propertySpec instanceof Reference) {
                continue;
            }

            $syntheticName = $this->createSyntheticSchemaName($parentSchemaName, $propertyName);
            $result = $this->processProperty($propertySpec, $syntheticName, $parentSchemaName, $propertyName, $group, $version);

            if (null !== $result) {
                $inlineSchemas[] = $result['schema'];

                // Add nested inline schemas recursively
                $inlineSchemas = [...$inlineSchemas, ...$result['nested']];
            }
        }

        return $inlineSchemas;
    }

    /**
     * Process a single property and check if it's an inline object.
     *
     * @param SchemaSpec $propertySpec The property specification (must be a Schema, not a Reference)
     * @param string     $group        The group from config
     * @param string     $version      The version from config
     *
     * @return array{schema: Schema, nested: array<Schema>}|null Returns schema and nested schemas, or null if not inline
     */
    private function processProperty(SchemaSpec $propertySpec, string $syntheticName, string $parentSchemaName, string $propertyName, string $group, string $version): ?array
    {
        // Check for direct inline object
        if ($this->isInlineObject($propertySpec)) {
            return $this->createInlineSchema($propertySpec, $syntheticName, $parentSchemaName, $propertyName, $group, $version);
        }

        // Handle arrays of inline objects
        if ('array' === $propertySpec->type && $propertySpec->items) {
            $itemSpec = $propertySpec->items;
            // Skip references
            if ($itemSpec instanceof Reference) {
                return null;
            }
            if ($this->isInlineObject($itemSpec)) {
                return $this->createInlineSchema($itemSpec, $syntheticName, $parentSchemaName, $propertyName, $group, $version);
            }
        }

        return null;
    }

    /**
     * Create a synthetic Schema object for an inline object.
     *
     * @return array{schema: Schema, nested: array<Schema>}
     */
    private function createInlineSchema(SchemaSpec $spec, string $syntheticName, string $parentSchemaName, string $propertyName, string $group, string $version): array
    {
        $schema = new Schema($syntheticName);
        $schema->setDescription($spec->description);

        // Generate class metadata based on synthetic name
        $classMetadata = $this->createClassMetadata($syntheticName, $group, $version);
        $schema->setClassMetadata($classMetadata);

        // Recursively process nested inline objects
        $nestedSchemas = $this->generate($spec, $syntheticName, $group, $version);

        return [
            'schema' => $schema,
            'nested' => $nestedSchemas,
        ];
    }

    /**
     * Create a synthetic schema name by appending property name to parent name.
     *
     * Examples:
     *   - Parent: 'com.example.food.v1alpha1.Pizzeria', Property: 'spec'
     *     -> 'com.example.food.v1alpha1.Pizzeria.spec'
     *   - Parent: 'com.example.food.v1alpha1.Pizzeria.spec', Property: 'chef'
     *     -> 'com.example.food.v1alpha1.Pizzeria.spec.chef'
     */
    private function createSyntheticSchemaName(string $parentName, string $propertyName): string
    {
        return $parentName.'.'.$propertyName;
    }

    /**
     * Create ClassMetadata for a synthetic schema using ClassMetadataExtractor.
     */
    private function createClassMetadata(string $syntheticName, string $group, string $version): ClassMetadata
    {
        return $this->classMetadataExtractor->extractForSyntheticSchema($syntheticName, $group, $version);
    }

    /**
     * Check if a SchemaSpec represents an inline object (not a named schema reference).
     *
     * An inline object has:
     * - type: 'object'
     * - properties defined
     * - is not a named schema from components/schemas
     */
    private function isInlineObject(SchemaSpec $spec): bool
    {
        return 'object' === $spec->type
            && !empty($spec->properties)
            && !$this->isNamedSchema($spec);
    }

    /**
     * Check if a SchemaSpec is a named schema from components/schemas.
     *
     * When cebe/openapi resolves a $ref, it replaces it with the actual schema object,
     * but preserves the document position. We can use this to detect if an object
     * is a named schema (depth 3 in components/schemas) versus an inline object.
     *
     * Example paths:
     * - Named schema: ['components', 'schemas', 'com.example.food.v1alpha1.Pizzeria']
     * - Inline object: ['components', 'schemas', 'com.example.food.v1alpha1.Pizzeria', 'properties', 'spec']
     */
    private function isNamedSchema(SchemaSpec $spec): bool
    {
        $path = $spec->getDocumentPosition()?->getPath() ?? [];

        // A named schema has exactly 3 elements in its path: ['components', 'schemas', 'SchemaName']
        return 3 === count($path)
            && isset($path[0], $path[1], $path[2])
            && 'components' === $path[0]
            && 'schemas' === $path[1];
    }
}
