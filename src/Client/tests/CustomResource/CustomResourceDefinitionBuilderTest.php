<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace P8p\Client\Tests\CustomResource;

use P8p\Client\CustomResource\CustomResourceDefinitionBuilder;
use P8p\Client\Exception\CrdException;
use P8p\Client\Tests\Fixtures\CustomResource\ComplexCustomResource;
use P8p\Client\Tests\Fixtures\CustomResource\IncompatibleCustomResource;
use P8p\Client\Tests\Fixtures\CustomResource\NoAttributeResource;
use P8p\Client\Tests\Fixtures\CustomResource\SimpleCustomResource;
use P8p\Client\Tests\Fixtures\CustomResource\SimpleCustomResourceV2;
use PHPUnit\Framework\TestCase;

class CustomResourceDefinitionBuilderTest extends TestCase
{
    private CustomResourceDefinitionBuilder $builder;

    protected function setUp(): void
    {
        if (!class_exists('P8p\Sdk\Schema\Apiextensions\V1\CustomResourceDefinition')) {
            $this->markTestSkipped('SDK package is required for these tests. Run: composer install');
        }

        $this->builder = new CustomResourceDefinitionBuilder();
    }

    public function testBuildWithSingleVersion(): void
    {
        $crd = $this->builder->build(SimpleCustomResource::class);

        $this->assertInstanceOf('P8p\Sdk\Schema\Apiextensions\V1\CustomResourceDefinition', $crd);
        $this->assertSame('example.com', $crd->spec->group);
        $this->assertSame('SimpleResource', $crd->spec->names->kind);
        $this->assertSame('simpleresources', $crd->spec->names->plural);
        $this->assertSame('simpleresource', $crd->spec->names->singular);
        $this->assertSame(['sr'], $crd->spec->names->shortNames);
        $this->assertSame('Namespaced', $crd->spec->scope);
        $this->assertCount(1, $crd->spec->versions);

        // Check version
        $version = $crd->spec->versions[0];
        $this->assertSame('v1', $version->name);
        $this->assertTrue($version->served);
        $this->assertTrue($version->storage); // Single version is always storage
    }

    public function testBuildWithMultipleVersions(): void
    {
        $crd = $this->builder->build([
            SimpleCustomResource::class,
            SimpleCustomResourceV2::class,
        ]);

        $this->assertInstanceOf('P8p\Sdk\Schema\Apiextensions\V1\CustomResourceDefinition', $crd);
        $this->assertCount(2, $crd->spec->versions);

        // First version
        $v1 = $crd->spec->versions[0];
        $this->assertSame('v1', $v1->name);
        $this->assertTrue($v1->served);
        $this->assertFalse($v1->storage); // Not the last version

        // Second version
        $v2 = $crd->spec->versions[1];
        $this->assertSame('v2', $v2->name);
        $this->assertTrue($v2->served);
        $this->assertTrue($v2->storage); // Last version is storage
    }

    public function testBuildWithClusterScopedResource(): void
    {
        $crd = $this->builder->build(ComplexCustomResource::class);

        $this->assertSame('Cluster', $crd->spec->scope);
    }

    public function testBuildGeneratesCorrectMetadataName(): void
    {
        $crd = $this->builder->build(SimpleCustomResource::class);

        $this->assertSame('simpleresources.example.com', $crd->metadata->name);
    }

    public function testBuildThrowsExceptionWithEmptyArray(): void
    {
        $this->expectException(CrdException::class);
        $this->expectExceptionMessage('At least one resource class must be provided');

        $this->builder->build([]);
    }

    public function testBuildThrowsExceptionWhenClassMissingAttribute(): void
    {
        $this->expectException(CrdException::class);
        $this->expectExceptionMessage('must have a #[K8sCustomResourceSchema] attribute');

        $this->builder->build(NoAttributeResource::class);
    }

    public function testBuildThrowsExceptionWithIncompatibleVersions(): void
    {
        $this->expectException(CrdException::class);
        $this->expectExceptionMessage('All resource classes must have the same group, kind, and plural name');

        $this->builder->build([
            SimpleCustomResource::class,
            IncompatibleCustomResource::class,
        ]);
    }

    public function testGeneratesOpenApiSchemaForStringType(): void
    {
        $crd = $this->builder->build(SimpleCustomResource::class);

        $schema = $crd->spec->versions[0]->schema->openAPIV3Schema;

        $this->assertSame('object', $schema['type']);
        $this->assertArrayHasKey('name', $schema['properties']);
        $this->assertSame('string', $schema['properties']['name']['type']);
    }

    public function testGeneratesOpenApiSchemaForIntegerType(): void
    {
        $crd = $this->builder->build(SimpleCustomResource::class);

        $schema = $crd->spec->versions[0]->schema->openAPIV3Schema;

        $this->assertArrayHasKey('replicas', $schema['properties']);
        $this->assertSame('integer', $schema['properties']['replicas']['type']);
        $this->assertSame('int32', $schema['properties']['replicas']['format']);
    }

    public function testGeneratesOpenApiSchemaForNullableType(): void
    {
        $crd = $this->builder->build(SimpleCustomResource::class);

        $schema = $crd->spec->versions[0]->schema->openAPIV3Schema;

        $this->assertArrayHasKey('description', $schema['properties']);
        $this->assertSame('string', $schema['properties']['description']['type']);
    }

    public function testGeneratesOpenApiSchemaForBooleanType(): void
    {
        $crd = $this->builder->build(ComplexCustomResource::class);

        $schema = $crd->spec->versions[0]->schema->openAPIV3Schema;

        $this->assertArrayHasKey('active', $schema['properties']);
        $this->assertSame('boolean', $schema['properties']['active']['type']);
    }

    public function testGeneratesOpenApiSchemaForFloatType(): void
    {
        $crd = $this->builder->build(ComplexCustomResource::class);

        $schema = $crd->spec->versions[0]->schema->openAPIV3Schema;

        $this->assertArrayHasKey('threshold', $schema['properties']);
        $this->assertSame('number', $schema['properties']['threshold']['type']);
        $this->assertSame('double', $schema['properties']['threshold']['format']);
    }

    public function testGeneratesOpenApiSchemaForArrayType(): void
    {
        $crd = $this->builder->build(ComplexCustomResource::class);

        $schema = $crd->spec->versions[0]->schema->openAPIV3Schema;

        $this->assertArrayHasKey('tags', $schema['properties']);
        $this->assertSame('array', $schema['properties']['tags']['type']);
        $this->assertArrayHasKey('items', $schema['properties']['tags']);
        $this->assertSame('string', $schema['properties']['tags']['items']['type']);
    }

    public function testGeneratesOpenApiSchemaForMixedArrayType(): void
    {
        $crd = $this->builder->build(ComplexCustomResource::class);

        $schema = $crd->spec->versions[0]->schema->openAPIV3Schema;

        $this->assertArrayHasKey('config', $schema['properties']);
        $this->assertSame('array', $schema['properties']['config']['type']);
        $this->assertArrayHasKey('items', $schema['properties']['config']);
        $this->assertSame('object', $schema['properties']['config']['items']['type']);
    }

    public function testGeneratesRequiredFieldsForNonNullableProperties(): void
    {
        $crd = $this->builder->build(SimpleCustomResource::class);

        $schema = $crd->spec->versions[0]->schema->openAPIV3Schema;

        $this->assertArrayHasKey('required', $schema);
        $this->assertContains('name', $schema['required']);
        $this->assertNotContains('description', $schema['required']); // Nullable
        // Note: promoted properties with defaults are still marked as required
        // because ReflectionProperty::hasDefaultValue() doesn't detect constructor defaults
        $this->assertContains('replicas', $schema['required']);
    }

    public function testSkipsStandardKubernetesProperties(): void
    {
        $crd = $this->builder->build(SimpleCustomResource::class);

        $schema = $crd->spec->versions[0]->schema->openAPIV3Schema;

        $this->assertArrayNotHasKey('apiVersion', $schema['properties']);
        $this->assertArrayNotHasKey('kind', $schema['properties']);
        $this->assertArrayNotHasKey('metadata', $schema['properties']);
    }

    public function testDifferentVersionsHaveDifferentSchemas(): void
    {
        $crd = $this->builder->build([
            SimpleCustomResource::class,
            SimpleCustomResourceV2::class,
        ]);

        $v1Schema = $crd->spec->versions[0]->schema->openAPIV3Schema;
        $v2Schema = $crd->spec->versions[1]->schema->openAPIV3Schema;

        // V1 doesn't have 'enabled' property
        $this->assertArrayNotHasKey('enabled', $v1Schema['properties']);

        // V2 has 'enabled' property
        $this->assertArrayHasKey('enabled', $v2Schema['properties']);
        $this->assertSame('boolean', $v2Schema['properties']['enabled']['type']);
    }
}
