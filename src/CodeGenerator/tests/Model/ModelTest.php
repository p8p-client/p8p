<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Tests\Model;

use P8p\CodeGenerator\Exception\ModelException;
use P8p\CodeGenerator\Model\Model;
use P8p\CodeGenerator\Model\Schema;
use P8p\CodeGenerator\Model\Service;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    // Tests for Schema management

    public function testAddSchema(): void
    {
        $model = new Model();
        $schema = new Schema('io.k8s.api.core.v1.Pod');

        $result = $model->addSchema($schema);

        $this->assertSame($model, $result); // Fluent interface
        $this->assertTrue($model->hasSchema('io.k8s.api.core.v1.Pod'));
    }

    public function testGetSchemas(): void
    {
        $model = new Model();
        $schema1 = new Schema('schema1');
        $schema2 = new Schema('schema2');

        $model->addSchema($schema1);
        $model->addSchema($schema2);

        $schemas = $model->getSchemas();

        $this->assertCount(2, $schemas);
        $this->assertSame($schema1, $schemas['schema1']);
        $this->assertSame($schema2, $schemas['schema2']);
    }

    public function testHasSchema(): void
    {
        $model = new Model();
        $schema = new Schema('test.schema');

        $this->assertFalse($model->hasSchema('test.schema'));

        $model->addSchema($schema);

        $this->assertTrue($model->hasSchema('test.schema'));
        $this->assertFalse($model->hasSchema('nonexistent'));
    }

    public function testGetSchema(): void
    {
        $model = new Model();
        $schema = new Schema('test.schema');
        $model->addSchema($schema);

        $retrieved = $model->getSchema('test.schema');

        $this->assertSame($schema, $retrieved);
    }

    public function testGetSchemaThrowsExceptionForNonexistentSchema(): void
    {
        $model = new Model();

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('The schema "nonexistent" does not exist');

        $model->getSchema('nonexistent');
    }

    public function testAddSchemaReplacesExistingSchema(): void
    {
        $model = new Model();
        $schema1 = new Schema('test.schema');
        $schema2 = new Schema('test.schema');

        $model->addSchema($schema1);
        $model->addSchema($schema2);

        $schemas = $model->getSchemas();
        $this->assertCount(1, $schemas);
        $this->assertSame($schema2, $schemas['test.schema']);
    }

    // Tests for Service management

    public function testAddService(): void
    {
        $model = new Model();
        $service = new Service('core.v1.pod');

        $result = $model->addService($service);

        $this->assertSame($model, $result); // Fluent interface
        $this->assertTrue($model->hasService('core.v1.pod'));
    }

    public function testGetServices(): void
    {
        $model = new Model();
        $service1 = new Service('service1');
        $service2 = new Service('service2');

        $model->addService($service1);
        $model->addService($service2);

        $services = $model->getServices();

        $this->assertCount(2, $services);
        $this->assertSame($service1, $services['service1']);
        $this->assertSame($service2, $services['service2']);
    }

    public function testHasService(): void
    {
        $model = new Model();
        $service = new Service('test.service');

        $this->assertFalse($model->hasService('test.service'));

        $model->addService($service);

        $this->assertTrue($model->hasService('test.service'));
        $this->assertFalse($model->hasService('nonexistent'));
    }

    public function testGetService(): void
    {
        $model = new Model();
        $service = new Service('test.service');
        $model->addService($service);

        $retrieved = $model->getService('test.service');

        $this->assertSame($service, $retrieved);
    }

    public function testGetServiceThrowsExceptionForNonexistentService(): void
    {
        $model = new Model();

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('The service "nonexistent" does not exist');

        $model->getService('nonexistent');
    }

    public function testAddServiceReplacesExistingService(): void
    {
        $model = new Model();
        $service1 = new Service('test.service');
        $service2 = new Service('test.service');

        $model->addService($service1);
        $model->addService($service2);

        $services = $model->getServices();
        $this->assertCount(1, $services);
        $this->assertSame($service2, $services['test.service']);
    }
}
