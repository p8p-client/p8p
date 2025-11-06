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

/**
 * Value object representing a path to an inline schema in OpenAPI document.
 *
 * Parses document paths from OpenAPI specs to extract schema names and property paths.
 * Example path: ['components', 'schemas', 'com.example.food.v1alpha1.Pizzeria', 'properties', 'spec', 'properties', 'chef']
 * Extracts: baseSchemaName = 'com.example.food.v1alpha1.Pizzeria', propertyPath = ['spec', 'chef']
 */
final readonly class SchemaPath
{
    /**
     * @param string   $baseSchemaName The base schema name (e.g., 'com.example.food.v1alpha1.Pizzeria')
     * @param string[] $propertyPath   The property path segments (e.g., ['spec', 'chef'])
     */
    public function __construct(
        public string $baseSchemaName,
        public array $propertyPath,
    ) {
    }

    /**
     * Create a SchemaPath from an OpenAPI document path.
     *
     * @param string[] $path Document path from OpenAPI spec
     *
     * @return self|null SchemaPath if valid, null otherwise
     */
    public static function fromDocumentPath(array $path): ?self
    {
        // Path must be at least 4 elements: ['components', 'schemas', 'SchemaName', 'properties', ...]
        if (count($path) < 4) {
            return null;
        }

        // Must start with ['components', 'schemas']
        if ('components' !== $path[0] || 'schemas' !== $path[1]) {
            return null;
        }

        $baseSchemaName = $path[2];
        $propertyPath = [];

        // Extract property path from remaining path elements
        // Pattern: 'properties', 'propertyName', ['properties', 'nestedProperty', ...]
        for ($i = 3; $i < count($path); ++$i) {
            if ('properties' === $path[$i] && isset($path[$i + 1])) {
                $propertyPath[] = $path[$i + 1];
                ++$i; // Skip the property name in next iteration
            }
        }

        return empty($propertyPath) ? null : new self($baseSchemaName, $propertyPath);
    }

    /**
     * Generate a synthetic schema name by combining base name and property path.
     *
     * Example: 'com.example.food.v1alpha1.Pizzeria' + ['spec', 'chef']
     *          -> 'com.example.food.v1alpha1.Pizzeria.spec.chef'
     */
    public function toSyntheticName(): string
    {
        return $this->baseSchemaName.'.'.implode('.', $this->propertyPath);
    }
}
