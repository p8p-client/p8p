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

class Model
{
    /** @var array<string, Schema> */
    private array $schemas = [];

    /** @var array<string, Service> */
    private array $services = [];

    /**
     * @return array<string, Schema>
     */
    public function getSchemas(): array
    {
        return $this->schemas;
    }

    public function addSchema(Schema $schema): self
    {
        $this->schemas[$schema->getName()] = $schema;

        return $this;
    }

    public function hasSchema(string $name): bool
    {
        return isset($this->schemas[$name]);
    }

    public function getSchema(string $name): Schema
    {
        if (!$this->hasSchema($name)) {
            throw new ModelException(sprintf('The schema "%s" does not exist "', $name));
        }

        return $this->schemas[$name];
    }

    /**
     * @return array<string, Service>
     */
    public function getServices(): array
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        $this->services[$service->getName()] = $service;

        return $this;
    }

    public function hasService(string $name): bool
    {
        return isset($this->services[$name]);
    }

    public function getService(string $name): Service
    {
        if (!$this->hasService($name)) {
            throw new ModelException(sprintf('The service "%s" does not exist "', $name));
        }

        return $this->services[$name];
    }
}
