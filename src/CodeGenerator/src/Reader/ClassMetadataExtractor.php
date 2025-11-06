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

use cebe\openapi\spec\Operation as OperationSpec;
use cebe\openapi\spec\Schema as SchemaSpec;
use P8p\CodeGenerator\Config\Config;
use P8p\CodeGenerator\Exception\ReaderException;
use P8p\CodeGenerator\Model\ClassMetadata;

use function Symfony\Component\String\u;

class ClassMetadataExtractor
{
    private const array RESERVED_WORDS = [
        'Namespace',
    ];

    public function __construct(private readonly Config $config)
    {
    }

    public function extractForSchema(SchemaSpec $schema, string $group, string $version): ClassMetadata
    {
        $kind = $this->extractKindFromSchemaName($schema);

        $normalizedGroup = $this->normalizeGroup($group);

        return new ClassMetadata(
            name: $this->generateClassName('Schema', $normalizedGroup, $version, $kind),
            path: $this->generateClassPath('Schema', $normalizedGroup, $version, $kind),
        );
    }

    public function extractForService(OperationSpec $spec, string $group, string $version): ClassMetadata
    {
        $kind = $this->extractKindFromOperation($spec);

        $normalizedGroup = $this->normalizeGroup($group);

        return new ClassMetadata(
            name: $this->generateClassName('Api', $normalizedGroup, $version, $kind, 'Api'),
            path: $this->generateClassPath('Api', $normalizedGroup, $version, $kind, 'Api'),
        );
    }

    /**
     * Extract ClassMetadata for a synthetic inline schema.
     *
     * @param string $syntheticName The synthetic schema name (e.g., 'com.example.food.v1alpha1.Pizzeria.spec.chef')
     * @param string $group         The group from config
     * @param string $version       The version from config
     */
    public function extractForSyntheticSchema(string $syntheticName, string $group, string $version): ClassMetadata
    {
        // k8s reverse group part in CRD
        $reverseGroupe = implode('.', array_reverse(explode('.', $group)));

        // Parse the synthetic name: reverseGroupe.version.kind.property1.property2...
        // remove <reverseGroupe>.<version>
        if (!str_starts_with($syntheticName, $reverseGroupe.'.'.$version)) {
            throw new ReaderException(sprintf('Synthetic schema name "%s" does not start with group "%s" and version "%s"', $syntheticName, $group, $version));
        }

        $parts = explode(
            '.',
            substr($syntheticName, strlen($reverseGroupe.'.'.$version) + 1)
        );

        // Extract kind and property path
        $kind = $parts[0];

        if ('' === $kind) {
            throw new \RuntimeException(sprintf('Unable to extract kind from synthetic schema name: %s', $syntheticName));
        }

        $propertyPath = array_slice($parts, 1);

        // Generate class name by combining kind and property path
        // Example: Pizzeria + spec + chef -> PizzeriaSpecChef
        $combinedKind = $kind;
        foreach ($propertyPath as $prop) {
            $combinedKind .= u($prop)->camel()->title()->toString();
        }

        $normalizedGroup = $this->normalizeGroup($group);

        return new ClassMetadata(
            name: $this->generateClassName('Schema', $normalizedGroup, $version, $combinedKind),
            path: $this->generateClassPath('Schema', $normalizedGroup, $version, $combinedKind),
        );
    }

    protected function generateClassName(string $type, string $group, string $version, string $kind, ?string $suffix = null): string
    {
        $baseNamespace = u($this->config->baseNamespace)->trim('\\');

        return implode('\\', [
            $baseNamespace,
            $type,
            $this->formatPart($group),
            $this->formatPart($version),
            $this->formatPart($kind).$suffix,
        ]);
    }

    protected function generateClassPath(string $type, string $group, string $version, string $kind, ?string $suffix = null): string
    {
        $baseNamespace = u($this->config->basePath)->trim('\\');

        return implode(DIRECTORY_SEPARATOR, [
            $baseNamespace,
            $type,
            $this->formatPart($group),
            $this->formatPart($version),
            $this->formatPart($kind).$suffix.'.php',
        ]);
    }

    private function normalizeGroup(string $group): string
    {
        if ('' === $group) {
            return 'core';
        }

        return u($group)->replace('.k8s.io', '')->toString();
    }

    private function extractKindFromSchemaName(SchemaSpec $schema): string
    {
        $name = $this->getSchemaName($schema);
        $parts = explode('.', $name);

        return array_pop($parts);
    }

    private function extractKindFromOperation(OperationSpec $spec): string
    {
        $extensions = $spec->getExtensions();

        if (!isset($extensions['x-kubernetes-group-version-kind'])) {
            throw new \RuntimeException(sprintf('Unable to extract kind for operation "%s"', $spec->operationId));
        }

        return $extensions['x-kubernetes-group-version-kind']['kind']; /* @phpstan-ignore offsetAccess.nonOffsetAccessible, return.type */
    }

    private function getSchemaName(SchemaSpec $schema): string
    {
        $part = $schema->getDocumentPosition()?->getPath() ?? [];

        return end($part);
    }

    private function formatPart(string $part): string
    {
        if (in_array($part, self::RESERVED_WORDS)) {
            $part .= 'K8s';
        }

        return u($part)->camel()->title()->toString();
    }
}
