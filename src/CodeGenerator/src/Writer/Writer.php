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

use P8p\CodeGenerator\Model\Model;

class Writer
{
    private readonly ClassDumper $classDumper;
    private readonly SchemaClassBuilder $schemaClassBuilder;

    private readonly ServiceClassBuilder $serviceClassBuilder;

    public function __construct()
    {
        $this->classDumper = new ClassDumper();
        $this->schemaClassBuilder = new SchemaClassBuilder();
        $this->serviceClassBuilder = new ServiceClassBuilder();
    }

    public function write(Model $model): void
    {
        foreach ($model->getSchemas() as $schema) {
            $this->classDumper->print($this->schemaClassBuilder->build($schema), $schema->getClassMetadata()->path);
        }

        foreach ($model->getServices() as $service) {
            $this->classDumper->print($this->serviceClassBuilder->build($service), $service->getClassMetadata()->path);
        }
    }
}
