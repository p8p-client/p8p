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

class Service
{
    /**
     * @var array<string, Operation>
     */
    private array $operations = [];

    private ?ClassMetadata $classMetadata = null;

    private ?GroupVersionKind $groupVersionKind = null;

    public function __construct(private readonly string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function hasOperation(string $name): bool
    {
        return isset($this->operations[$name]);
    }

    public function getOperation(string $name): Operation
    {
        if (!$this->hasOperation($name)) {
            throw new ModelException(sprintf('Operation "%s" does not exist "', $name));
        }

        return $this->operations[$name];
    }

    /**
     * @return array<string, Operation>
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): self
    {
        $this->operations[$operation->operationId] = $operation;

        return $this;
    }

    public function getClassMetadata(): ClassMetadata
    {
        if (!$this->classMetadata) {
            throw new ModelException('Class metadata is not set for service '.$this->name);
        }

        return $this->classMetadata;
    }

    public function setClassMetadata(ClassMetadata $classMetadata): self
    {
        $this->classMetadata = $classMetadata;

        return $this;
    }

    public function getGroupVersionKind(): GroupVersionKind
    {
        if (!$this->groupVersionKind) {
            throw new ModelException('Group version kind is not set for service '.$this->name);
        }

        return $this->groupVersionKind;
    }

    public function setGroupVersionKind(GroupVersionKind $groupVersionKind): self
    {
        $this->groupVersionKind = $groupVersionKind;

        return $this;
    }
}
