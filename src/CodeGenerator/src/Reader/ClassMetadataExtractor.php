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

    public function extractForSchema(SchemaSpec $schema): ClassMetadata
    {
        [$group, $version, $kind] = $this->extractSchemaGroupVersionKind($schema);

        return new ClassMetadata(
            name: $this->generateClassName('Schema', $group, $version, $kind),
            path: $this->generateClassPath('Schema', $group, $version, $kind),
        );
    }

    public function extractForService(OperationSpec $spec): ClassMetadata
    {
        [$group, $version, $kind] = $this->extractServiceGroupVersionKind($spec);

        return new ClassMetadata(
            name: $this->generateClassName('Api', $group, $version, $kind, 'Api'),
            path: $this->generateClassPath('Api', $group, $version, $kind, 'Api'),
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

    /**
     * @return array<int, string>
     */
    private function extractSchemaGroupVersionKind(SchemaSpec $spec): array
    {
        $name = $this->getSchemaName($spec);
        $parts = explode('.', $name);

        $kind = array_pop($parts);
        $version = array_pop($parts);
        $group = array_pop($parts);

        return [$group, $version, $kind]; /* @phpstan-ignore return.type */
    }

    /**
     * @return array<string>
     */
    protected function extractServiceGroupVersionKind(OperationSpec $spec): array
    {
        $extensions = $spec->getExtensions();

        if (!isset($extensions['x-kubernetes-group-version-kind'])) {
            throw new \RuntimeException(sprintf('Unable to extract group version kind for operation "%s"', $spec->operationId));
        }

        /** @var array{
         *     x-kubernetes-group-version-kind: array{
         *         group: string,
         *         version: string,
         *         kind: string
         *     }
         * } $extensions */
        $group = $extensions['x-kubernetes-group-version-kind']['group'];
        $version = $extensions['x-kubernetes-group-version-kind']['version'];
        $kind = $extensions['x-kubernetes-group-version-kind']['kind'];

        if ('' === $group) {
            $group = 'core';
        } else {
            $group = u($group)->replace('.k8s.io', '')->toString();
        }

        return [$group, $version, $kind];
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
