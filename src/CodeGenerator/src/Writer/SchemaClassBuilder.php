<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Writer;

use Nette\PhpGenerator\PhpNamespace;
use P8p\Client\Attribute\K8sSchema;
use P8p\Client\Attribute\K8sSchemaRef;
use P8p\CodeGenerator\Model\Schema;

class SchemaClassBuilder
{
    public function build(Schema $schema): PhpNamespace
    {
        $namespace = new PhpNamespace($schema->getClassMetadata()->getNamespace());
        $classType = $namespace->addClass($schema->getClassMetadata()->getShortName());
        $constructor = $classType->addMethod('__construct');

        foreach ($this->extractUse($schema) as $use) {
            $namespace->addUse($use);
        }

        // Add K8sSchemaRef to all schemas (technical mapping)
        $namespace->addUse(K8sSchemaRef::class);
        $classType->addAttribute(K8sSchemaRef::class, [
            'name' => $schema->getName(),
        ]);

        // Add K8sSchema only for resources with GVK (used by normalizer)
        if ($schema->getGroupVersionKind() && $schema->hasProperty('kind') && $schema->hasProperty('apiVersion')) {
            $namespace->addUse(K8sSchema::class);
            $classType->addAttribute(K8sSchema::class, [
                'kind' => $schema->getGroupVersionKind()->kind,
                'group' => $schema->getGroupVersionKind()->group,
                'version' => $schema->getGroupVersionKind()->version,
            ]);
        }

        foreach ($schema->getProperties() as $property) {
            if ($schema->getGroupVersionKind() && ('kind' === $property->name || 'apiVersion' === $property->name)) {
                continue;
            }

            if ($property->type->isNullable()) {
                $parameter = $constructor->addPromotedParameter($property->name, null);
            } else {
                $parameter = $constructor->addPromotedParameter($property->name);
            }
            $parameter->setType(TypeFormatter::toPhpType($property->type));

            // Build PHPDoc annotation
            $phpDocType = TypeFormatter::toPhpDocType($property->type);
            $phpType = TypeFormatter::toPhpType($property->type);

            // Use PHPDoc type if different from PHP type (e.g., collections with generics)
            $typeForDoc = $phpDocType !== $phpType ? $phpDocType : $phpType;

            $constructor->addComment("@param {$typeForDoc} \${$property->name}".($property->description ? " {$property->description}" : ''));
        }

        return $namespace;
    }

    /**
     * @return array<string>
     */
    public function extractUse(Schema $schema): array
    {
        $uses = [];

        foreach ($schema->getProperties() as $property) {
            $uses = [...$uses, ...TypeFormatter::extractAllClassNames($property->type)];
        }

        return array_unique($uses);
    }
}
