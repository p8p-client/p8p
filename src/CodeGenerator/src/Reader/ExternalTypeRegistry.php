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

use Nette\PhpGenerator\PhpFile;
use P8p\CodeGenerator\Exception\ReaderException;
use Symfony\Component\Finder\Finder;

/**
 * Registry that scans an external SDK directory to discover existing types.
 * Prevents duplication of common Kubernetes types (ObjectMeta, TypeMeta, etc.).
 *
 * Works by parsing generated schema files to extract their K8sSchemaRef attributes.
 */
class ExternalTypeRegistry
{
    /**
     * @var array<string, string> Maps OpenAPI schema name to PHP FQCN
     */
    private array $schemaNameToFqcn = [];

    /**
     * Scan a Schema directory to discover all existing classes.
     * Builds a mapping from OpenAPI schema names to PHP FQCNs by parsing
     * the K8sSchemaRef attribute in each file.
     *
     * @param string $basePath Base directory path (e.g., '/path/to/sdk/src')
     */
    public function scan(string $basePath): void
    {
        $schemaPath = rtrim($basePath, '/').'/Schema';

        if (!is_dir($schemaPath)) {
            throw new \RuntimeException(sprintf('Schema directory "%s" does not exist', $schemaPath));
        }

        $finder = new Finder();
        $finder->files()->in($schemaPath)->name('*.php');

        foreach ($finder as $file) {
            $this->processFile($file->getPathname());
        }
    }

    /**
     * Resolve a schema name to its FQCN if it exists in the registry.
     *
     * @param string $schemaName OpenAPI schema name (e.g., 'io.k8s.api.core.v1.Pod')
     *
     * @return string|null FQCN if found, null otherwise
     */
    public function resolveSchemaName(string $schemaName): ?string
    {
        return $this->schemaNameToFqcn[$schemaName] ?? null;
    }

    /**
     * Check if a schema exists in the registry.
     */
    public function hasSchema(string $schemaName): bool
    {
        return isset($this->schemaNameToFqcn[$schemaName]);
    }

    /**
     * Process a PHP file to extract K8sSchemaRef attribute and register it.
     *
     * Uses Nette PhpGenerator to parse the file and extract both the FQCN
     * and the K8sSchemaRef attribute value.
     */
    private function processFile(string $filePath): void
    {
        $content = file_get_contents($filePath);
        if (false === $content) {
            return;
        }

        try {
            $phpFile = PhpFile::fromCode($content);
            $classes = $phpFile->getClasses();

            if (empty($classes)) {
                return;
            }

            // Get the first (and usually only) class in the file
            $class = reset($classes);

            // Get namespace
            $namespace = $phpFile->getNamespaces();
            if (empty($namespace)) {
                return;
            }
            $namespaceObj = reset($namespace);

            // Build FQCN (namespace + class name)
            $namespaceName = $namespaceObj->getName();
            $className = $class->getName();

            if (null === $className) {
                return;
            }

            $fqcn = $namespaceName ? $namespaceName.'\\'.$className : $className;

            // Look for K8sSchemaRef attribute
            foreach ($class->getAttributes() as $attribute) {
                if (str_ends_with((string) $attribute->getName(), 'K8sSchemaRef')) {
                    $args = $attribute->getArguments();
                    $schemaName = $args['name'] ?? $args[0] ?? null;

                    if (is_string($schemaName)) {
                        $this->schemaNameToFqcn[$schemaName] = $fqcn;
                    }
                    break;
                }
            }
        } catch (\Exception) {
            throw new ReaderException(sprintf('Unable to parse file "%s"', $filePath));
        }
    }
}
