<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Model;

use P8p\CodeGenerator\Exception\ModelException;

class Schema
{
    private ?GroupVersionKind $groupVersionKind = null;

    private ?ClassMetadata $classMetadata = null;

    private ?string $description = null;

    /** @var array<string, Property> */
    private array $properties = [];

    public function __construct(
        private readonly string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClassMetadata(): ClassMetadata
    {
        if (!$this->classMetadata) {
            throw new ModelException('Class metadata is not set for schema '.$this->name);
        }

        return $this->classMetadata;
    }

    public function setClassMetadata(ClassMetadata $classMetadata): self
    {
        $this->classMetadata = $classMetadata;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getGroupVersionKind(): ?GroupVersionKind
    {
        return $this->groupVersionKind;
    }

    public function setGroupVersionKind(?GroupVersionKind $groupVersionKind): self
    {
        $this->groupVersionKind = $groupVersionKind;

        return $this;
    }

    /**
     * @return array<string, Property>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function addProperty(Property $property): self
    {
        $this->properties[$property->name] = $property;

        return $this;
    }

    public function hasProperty(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    public function getProperty(string $name): Property
    {
        if (!$this->hasProperty($name)) {
            throw new ModelException(sprintf('The property "%s" does not exist "', $name));
        }

        return $this->properties[$name];
    }

    public function reorderProperties(): void
    {
        uasort($this->properties, function (Property $a, Property $b) {
            if ($a->type->isNullable() == $b->type->isNullable()) {
                return 0;
            }

            return $a->type->isNullable() ? 1 : -1;
        });
    }
}
