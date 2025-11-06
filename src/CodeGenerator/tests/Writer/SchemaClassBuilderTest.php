<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Tests\Writer;

use P8p\CodeGenerator\Model\ClassMetadata;
use P8p\CodeGenerator\Model\GroupVersionKind;
use P8p\CodeGenerator\Model\Property;
use P8p\CodeGenerator\Model\Schema;
use P8p\CodeGenerator\Writer\SchemaClassBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

class SchemaClassBuilderTest extends TestCase
{
    private SchemaClassBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new SchemaClassBuilder();
    }

    public function testBuildSimpleSchema(): void
    {
        $schema = new Schema('io.k8s.api.core.v1.Pod');
        $schema->setClassMetadata(new ClassMetadata('App\\Schema\\Core\\V1\\Pod', '/path/to/Pod.php'));
        $schema->addProperty(new Property('name', 'The pod name', Type::string()));
        $schema->addProperty(new Property('replicas', 'Number of replicas', Type::int()));

        $namespace = $this->builder->build($schema);

        $this->assertSame('App\\Schema\\Core\\V1', $namespace->getName());

        $classes = $namespace->getClasses();
        $this->assertArrayHasKey('Pod', $classes);

        $class = $classes['Pod'];
        $this->assertTrue($class->hasMethod('__construct'));

        $attributes = $class->getAttributes();
        $this->assertCount(1, $attributes);
        $this->assertSame('P8p\\Client\\Attribute\\K8sSchemaRef', $attributes[0]->getName());
        $schemaRefArgs = $attributes[0]->getArguments();
        $this->assertEquals('io.k8s.api.core.v1.Pod', $schemaRefArgs['name']);

        $constructor = $class->getMethod('__construct');
        $parameters = $constructor->getParameters();

        $this->assertCount(2, $parameters);
        $this->assertArrayHasKey('name', $parameters);
        $this->assertArrayHasKey('replicas', $parameters);

        $this->assertSame('string', $parameters['name']->getType());
        $this->assertSame('int', $parameters['replicas']->getType());
    }

    public function testBuildSchemaWithNullableProperties(): void
    {
        $schema = new Schema('io.k8s.api.core.v1.Pod');
        $schema->setClassMetadata(new ClassMetadata('App\\Schema\\Core\\V1\\Pod', '/path/to/Pod.php'));
        $schema->addProperty(new Property('name', null, Type::string()));
        $schema->addProperty(new Property('description', null, Type::nullable(Type::string())));

        $namespace = $this->builder->build($schema);

        $classes = $namespace->getClasses();
        $class = $classes['Pod'];
        $constructor = $class->getMethod('__construct');
        $parameters = $constructor->getParameters();

        $this->assertFalse($parameters['name']->hasDefaultValue());
        $this->assertTrue($parameters['description']->hasDefaultValue());
        $this->assertNull($parameters['description']->getDefaultValue());
    }

    public function testBuildSchemaWithObjectTypeProperty(): void
    {
        $schema = new Schema('io.k8s.api.core.v1.Pod');
        $schema->setClassMetadata(new ClassMetadata('App\\Schema\\Core\\V1\\Pod', '/path/to/Pod.php'));
        $schema->addProperty(new Property('metadata', null, Type::object('App\\Schema\\Meta\\V1\\ObjectMeta')));

        $namespace = $this->builder->build($schema);

        $uses = $namespace->getUses();
        $this->assertContains('App\\Schema\\Meta\\V1\\ObjectMeta', $uses);

        $classes = $namespace->getClasses();
        $class = $classes['Pod'];
        $constructor = $class->getMethod('__construct');
        $parameters = $constructor->getParameters();

        $this->assertSame('App\\Schema\\Meta\\V1\\ObjectMeta', $parameters['metadata']->getType());
    }

    public function testBuildSchemaWithListProperty(): void
    {
        $schema = new Schema('io.k8s.api.core.v1.PodList');
        $schema->setClassMetadata(new ClassMetadata('App\\Schema\\Core\\V1\\PodList', '/path/to/PodList.php'));
        $schema->addProperty(new Property('items', null, Type::list(Type::object('App\\Schema\\Core\\V1\\Pod'))));

        $namespace = $this->builder->build($schema);

        $classes = $namespace->getClasses();
        $class = $classes['PodList'];
        $constructor = $class->getMethod('__construct');
        $parameters = $constructor->getParameters();

        $this->assertSame('array', $parameters['items']->getType());
    }

    public function testBuildSchemaWithGroupVersionKind(): void
    {
        $schema = new Schema('io.k8s.api.core.v1.Pod');
        $schema->setClassMetadata(new ClassMetadata('App\\Schema\\Core\\V1\\Pod', '/path/to/Pod.php'));
        $schema->setGroupVersionKind(new GroupVersionKind('', 'v1', 'Pod'));
        $schema->addProperty(new Property('kind', null, Type::string()));
        $schema->addProperty(new Property('apiVersion', null, Type::string()));
        $schema->addProperty(new Property('metadata', null, Type::object('App\\Schema\\Meta\\V1\\ObjectMeta')));

        $namespace = $this->builder->build($schema);

        $classes = $namespace->getClasses();
        $class = $classes['Pod'];

        // Check that both K8sSchemaRef and K8sSchema attributes are present
        $attributes = $class->getAttributes();
        $this->assertCount(2, $attributes);

        // K8sSchemaRef is always added
        $this->assertSame('P8p\\Client\\Attribute\\K8sSchemaRef', $attributes[0]->getName());
        $schemaRefArgs = $attributes[0]->getArguments();
        $this->assertEquals('io.k8s.api.core.v1.Pod', $schemaRefArgs['name']);

        // K8sSchema is added for resources with GVK
        $this->assertSame('P8p\\Client\\Attribute\\K8sSchema', $attributes[1]->getName());
        $schemaArgs = $attributes[1]->getArguments();
        $this->assertEquals('Pod', $schemaArgs['kind']);
        $this->assertEquals('', $schemaArgs['group']);
        $this->assertEquals('v1', $schemaArgs['version']);

        // Check that kind and apiVersion are NOT in constructor (excluded for GVK schemas)
        $constructor = $class->getMethod('__construct');
        $parameters = $constructor->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertArrayHasKey('metadata', $parameters);
        $this->assertArrayNotHasKey('kind', $parameters);
        $this->assertArrayNotHasKey('apiVersion', $parameters);
    }

    public function testBuildSchemaWithUnionType(): void
    {
        $schema = new Schema('io.k8s.api.core.v1.Resource');
        $schema->setClassMetadata(new ClassMetadata('App\\Schema\\Core\\V1\\Resource', '/path/to/Resource.php'));
        $schema->addProperty(new Property('value', null, Type::union(Type::string(), Type::int())));

        $namespace = $this->builder->build($schema);

        $classes = $namespace->getClasses();
        $class = $classes['Resource'];
        $constructor = $class->getMethod('__construct');
        $parameters = $constructor->getParameters();

        $this->assertSame('int|string', $parameters['value']->getType());
    }

    public function testExtractUseReturnsUniqueClassNames(): void
    {
        $schema = new Schema('io.k8s.api.core.v1.Pod');
        $schema->setClassMetadata(new ClassMetadata('App\\Schema\\Core\\V1\\Pod', '/path/to/Pod.php'));
        $schema->addProperty(new Property('metadata', null, Type::object('App\\Schema\\Meta\\V1\\ObjectMeta')));
        $schema->addProperty(new Property('spec', null, Type::object('App\\Schema\\Core\\V1\\PodSpec')));
        $schema->addProperty(new Property('status', null, Type::object('App\\Schema\\Core\\V1\\PodSpec'))); // duplicate

        $uses = $this->builder->extractUse($schema);

        $this->assertCount(2, $uses);
        $this->assertContains('App\\Schema\\Meta\\V1\\ObjectMeta', $uses);
        $this->assertContains('App\\Schema\\Core\\V1\\PodSpec', $uses);
    }
}
