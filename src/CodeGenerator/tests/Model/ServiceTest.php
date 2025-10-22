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
use P8p\CodeGenerator\Model\ClassMetadata;
use P8p\CodeGenerator\Model\GroupVersionKind;
use P8p\CodeGenerator\Model\Operation;
use P8p\CodeGenerator\Model\Service;
use P8p\CodeGenerator\Model\VerbEnum;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    public function testConstructorSetsName(): void
    {
        $service = new Service('core.v1.pod');

        $this->assertSame('core.v1.pod', $service->getName());
    }

    public function testAddOperation(): void
    {
        $service = new Service('test.service');
        $operation = new Operation(
            operationId: 'listPods',
            name: 'list',
            path: '/api/v1/pods',
            verb: VerbEnum::GET,
            groupVersionKind: new GroupVersionKind('core', 'v1', 'Pod'),
            type: 'list'
        );

        $result = $service->addOperation($operation);

        $this->assertSame($service, $result); // Fluent interface
        $this->assertTrue($service->hasOperation('listPods'));
    }

    public function testGetOperations(): void
    {
        $service = new Service('test.service');
        $op1 = new Operation(
            operationId: 'list',
            name: 'list',
            path: '/api/v1/pods',
            verb: VerbEnum::GET,
            groupVersionKind: new GroupVersionKind('core', 'v1', 'Pod'),
            type: 'list'
        );
        $op2 = new Operation(
            operationId: 'create',
            name: 'create',
            path: '/api/v1/pods',
            verb: VerbEnum::POST,
            groupVersionKind: new GroupVersionKind('core', 'v1', 'Pod'),
            type: 'create'
        );

        $service->addOperation($op1);
        $service->addOperation($op2);

        $operations = $service->getOperations();

        $this->assertCount(2, $operations);
        $this->assertSame($op1, $operations['list']);
        $this->assertSame($op2, $operations['create']);
    }

    public function testHasOperation(): void
    {
        $service = new Service('test.service');
        $operation = new Operation(
            operationId: 'list',
            name: 'list',
            path: '/api/v1/pods',
            verb: VerbEnum::GET,
            groupVersionKind: new GroupVersionKind('core', 'v1', 'Pod'),
            type: 'list'
        );

        $this->assertFalse($service->hasOperation('list'));

        $service->addOperation($operation);

        $this->assertTrue($service->hasOperation('list'));
        $this->assertFalse($service->hasOperation('nonexistent'));
    }

    public function testGetOperation(): void
    {
        $service = new Service('test.service');
        $operation = new Operation(
            operationId: 'list',
            name: 'list',
            path: '/api/v1/pods',
            verb: VerbEnum::GET,
            groupVersionKind: new GroupVersionKind('core', 'v1', 'Pod'),
            type: 'list'
        );
        $service->addOperation($operation);

        $retrieved = $service->getOperation('list');

        $this->assertSame($operation, $retrieved);
    }

    public function testGetOperationThrowsExceptionForNonexistentOperation(): void
    {
        $service = new Service('test.service');

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('Operation "nonexistent" does not exist');

        $service->getOperation('nonexistent');
    }

    public function testSetAndGetClassMetadata(): void
    {
        $service = new Service('test.service');
        $metadata = new ClassMetadata('Test\\Service', '/path/to/Service.php');

        $result = $service->setClassMetadata($metadata);

        $this->assertSame($service, $result); // Fluent interface
        $this->assertSame($metadata, $service->getClassMetadata());
    }
}
