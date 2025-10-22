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
use P8p\CodeGenerator\Model\Property;
use P8p\CodeGenerator\Model\Schema;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

class SchemaTest extends TestCase
{
    public function testConstructorSetsName(): void
    {
        $schema = new Schema('io.k8s.api.core.v1.Pod');

        $this->assertSame('io.k8s.api.core.v1.Pod', $schema->getName());
    }

    public function testSetAndGetDescription(): void
    {
        $schema = new Schema('test.schema');
        $schema->setDescription('This is a test schema');

        $this->assertSame('This is a test schema', $schema->getDescription());
    }

    public function testSetAndGetClassMetadata(): void
    {
        $schema = new Schema('test.schema');
        $metadata = new ClassMetadata('Test\\Schema', '/path/to/Schema.php');

        $schema->setClassMetadata($metadata);

        $this->assertSame($metadata, $schema->getClassMetadata());
    }

    public function testSetAndGetGroupVersionKind(): void
    {
        $schema = new Schema('test.schema');
        $gvk = new GroupVersionKind('apps', 'v1', 'Deployment');

        $schema->setGroupVersionKind($gvk);

        $this->assertSame($gvk, $schema->getGroupVersionKind());
    }

    public function testAddProperty(): void
    {
        $schema = new Schema('test.schema');
        $property = new Property('name', 'The name', Type::string());

        $result = $schema->addProperty($property);

        $this->assertSame($schema, $result); // Fluent interface
        $this->assertTrue($schema->hasProperty('name'));
    }

    public function testGetProperties(): void
    {
        $schema = new Schema('test.schema');
        $property1 = new Property('name', null, Type::string());
        $property2 = new Property('count', null, Type::int());

        $schema->addProperty($property1);
        $schema->addProperty($property2);

        $properties = $schema->getProperties();

        $this->assertCount(2, $properties);
        $this->assertSame($property1, $properties['name']);
        $this->assertSame($property2, $properties['count']);
    }

    public function testHasProperty(): void
    {
        $schema = new Schema('test.schema');
        $property = new Property('name', null, Type::string());

        $this->assertFalse($schema->hasProperty('name'));

        $schema->addProperty($property);

        $this->assertTrue($schema->hasProperty('name'));
        $this->assertFalse($schema->hasProperty('nonexistent'));
    }

    public function testGetProperty(): void
    {
        $schema = new Schema('test.schema');
        $property = new Property('name', null, Type::string());
        $schema->addProperty($property);

        $retrieved = $schema->getProperty('name');

        $this->assertSame($property, $retrieved);
    }

    public function testGetPropertyThrowsExceptionForNonexistentProperty(): void
    {
        $schema = new Schema('test.schema');

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('The property "nonexistent" does not exist');

        $schema->getProperty('nonexistent');
    }

    public function testReorderPropertiesMovesNullableToEnd(): void
    {
        $schema = new Schema('test.schema');

        // Add properties: required, nullable, required
        $prop1 = new Property('name', null, Type::string());
        $prop2 = new Property('optional', null, Type::nullable(Type::int()));
        $prop3 = new Property('count', null, Type::int());

        $schema->addProperty($prop1);
        $schema->addProperty($prop2);
        $schema->addProperty($prop3);

        $schema->reorderProperties();

        $properties = array_values($schema->getProperties());

        // Non-nullable should come first
        $this->assertSame('name', $properties[0]->name);
        $this->assertSame('count', $properties[1]->name);
        $this->assertSame('optional', $properties[2]->name);
    }
}
