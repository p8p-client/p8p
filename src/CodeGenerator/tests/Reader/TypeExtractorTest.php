<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Tests\Reader;

use cebe\openapi\json\JsonPointer;
use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Schema as OpenApiSchema;
use P8p\CodeGenerator\Config\Api;
use P8p\CodeGenerator\Config\Config;
use P8p\CodeGenerator\Exception\ReaderException;
use P8p\CodeGenerator\Model\ClassMetadata;
use P8p\CodeGenerator\Model\Model;
use P8p\CodeGenerator\Model\Schema;
use P8p\CodeGenerator\Reader\ExternalTypeRegistry;
use P8p\CodeGenerator\Reader\TypeExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

class TypeExtractorTest extends TestCase
{
    private TypeExtractor $extractor;
    private Config $config;
    private Model $model;
    private string $fixturesPath;
    private ExternalTypeRegistry $externalTypeRegistry;

    protected function setUp(): void
    {
        $this->fixturesPath = __DIR__.'/../Fixtures/openapi';
        $this->config = new Config(
            baseNamespace: 'P8p\\Sdk',
            basePath: '/path/to/sdk',
            apis: [new Api('apiextensions.k8s.io', 'v1')],
            schemasOverride: [],
            documentationOutputDir: '/path/to/sdk/docs',
            documentationTemplateDir: __DIR__.'/../Fixtures/templates',
        );
        $this->externalTypeRegistry = new ExternalTypeRegistry();
        $classMetadataExtractor = new \P8p\CodeGenerator\Reader\ClassMetadataExtractor($this->config);
        $inlineSchemaGenerator = new \P8p\CodeGenerator\Reader\InlineSchemaGenerator($classMetadataExtractor);
        $this->extractor = new TypeExtractor($this->config, $this->externalTypeRegistry, $inlineSchemaGenerator);
        $this->model = new Model();
    }

    // Tests with real OpenAPI spec

    public function testExtractStringType(): void
    {
        $columnDefSchema = $this->getSchema('io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.CustomResourceColumnDefinition');
        /** @var OpenApiSchema $nameProperty */
        $nameProperty = $columnDefSchema->properties['name'];
        $type = $this->extractor->extract($nameProperty, $this->model, 'apiextensions.k8s.io', 'v1');

        $this->assertInstanceOf(Type::class, $type);
        $this->assertEquals('string', (string) $type);
    }

    public function testExtractIntegerType(): void
    {
        $columnDefSchema = $this->getSchema('io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.CustomResourceColumnDefinition');
        /** @var OpenApiSchema $priorityProperty */
        $priorityProperty = $columnDefSchema->properties['priority'];

        $type = $this->extractor->extract($priorityProperty, $this->model, 'apiextensions.k8s.io', 'v1');

        $this->assertInstanceOf(Type::class, $type);
        $this->assertEquals('int', (string) $type);
    }

    public function testExtractArrayType(): void
    {
        $namesSchema = $this->getSchema('io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.CustomResourceDefinitionNames');
        /** @var OpenApiSchema $shortNamesProperty */
        $shortNamesProperty = $namesSchema->properties['shortNames'];

        $type = $this->extractor->extract($shortNamesProperty, $this->model, 'apiextensions.k8s.io', 'v1');

        $this->assertInstanceOf(Type::class, $type);
        $this->assertEquals('list<string>', (string) $type);
    }

    public function testExtractObjectReference(): void
    {
        $openApi = Reader::readFromJsonFile($this->fixturesPath.'/apiextensionsV1.json');

        $objectMetaSchema = new Schema('io.k8s.apimachinery.pkg.apis.meta.v1.ObjectMeta');
        $objectMetaSchema->setClassMetadata(new ClassMetadata('P8p\\Sdk\\Schema\\Meta\\V1\\ObjectMeta', '/path/to/ObjectMeta.php'));
        $this->model->addSchema($objectMetaSchema);

        /** @var OpenApiSchema $crdSchema */
        $crdSchema = $openApi->components?->schemas['io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.CustomResourceDefinition'];

        /** @var OpenApiSchema $metadataProperty */
        $metadataProperty = $crdSchema->properties['metadata'];

        $type = $this->extractor->extract($metadataProperty, $this->model, 'apiextensions.k8s.io', 'v1');

        $this->assertInstanceOf(Type::class, $type);
        $this->assertEquals('P8p\\Sdk\\Schema\\Meta\\V1\\ObjectMeta', (string) $type);
    }

    public function testExtractUsesSchemaOverride(): void
    {
        $overrideType = Type::union(Type::int(), Type::string());
        $this->config = new Config(
            baseNamespace: 'P8p\\Sdk',
            basePath: '/path/to/sdk',
            apis: [],
            schemasOverride: ['io.k8s.IntOrString' => $overrideType],
            documentationOutputDir: '/path/to/sdk/docs',
            documentationTemplateDir: __DIR__.'/../Fixtures/templates',
        );
        $this->externalTypeRegistry = new ExternalTypeRegistry();
        $classMetadataExtractor = new \P8p\CodeGenerator\Reader\ClassMetadataExtractor($this->config);
        $inlineSchemaGenerator = new \P8p\CodeGenerator\Reader\InlineSchemaGenerator($classMetadataExtractor);
        $this->extractor = new TypeExtractor($this->config, $this->externalTypeRegistry, $inlineSchemaGenerator);

        $openApiSchema = $this->createOpenApiSchemaWithPath(['components', 'schemas', 'io.k8s.IntOrString']);

        $type = $this->extractor->extract($openApiSchema, $this->model, 'apiextensions.k8s.io', 'v1');

        $this->assertSame($overrideType, $type);
    }

    public function testExtractUsesExternalTypeRegistry(): void
    {
        // Scan external SDK fixtures
        $this->externalTypeRegistry->scan(__DIR__.'/../Fixtures/external-sdk');
        $classMetadataExtractor = new \P8p\CodeGenerator\Reader\ClassMetadataExtractor($this->config);
        $inlineSchemaGenerator = new \P8p\CodeGenerator\Reader\InlineSchemaGenerator($classMetadataExtractor);
        $this->extractor = new TypeExtractor($this->config, $this->externalTypeRegistry, $inlineSchemaGenerator);

        $openApiSchema = $this->createOpenApiSchemaWithPath(['components', 'schemas', 'io.k8s.api.core.v1.Pod']);

        $type = $this->extractor->extract($openApiSchema, $this->model, 'apiextensions.k8s.io', 'v1');

        $this->assertInstanceOf(Type::class, $type);
        $this->assertEquals('P8p\\Sdk\\Schema\\Core\\V1\\TestPod', (string) $type);
    }

    public function testMapStringType(): void
    {
        $schema = $this->createOpenApiSchema(['type' => 'string']);

        $type = $this->extractor->extract($schema, $this->model, 'apiextensions.k8s.io', 'v1');

        $this->assertInstanceOf(Type::class, $type);
        $this->assertStringContainsString('string', (string) $type);
    }

    public function testMapIntegerType(): void
    {
        $schema = $this->createOpenApiSchema(['type' => 'integer']);

        $type = $this->extractor->extract($schema, $this->model, 'apiextensions.k8s.io', 'v1');

        $this->assertInstanceOf(Type::class, $type);
        $this->assertStringContainsString('int', (string) $type);
    }

    public function testMapBooleanType(): void
    {
        $schema = $this->createOpenApiSchema(['type' => 'boolean']);

        $type = $this->extractor->extract($schema, $this->model, 'apiextensions.k8s.io', 'v1');

        $this->assertInstanceOf(Type::class, $type);
        $this->assertStringContainsString('bool', (string) $type);
    }

    public function testMapNumberType(): void
    {
        $schema = $this->createOpenApiSchema(['type' => 'number']);

        $type = $this->extractor->extract($schema, $this->model, 'apiextensions.k8s.io', 'v1');

        $this->assertInstanceOf(Type::class, $type);
        $this->assertStringContainsString('float', (string) $type);
    }

    public function testMapArrayType(): void
    {
        $schema = $this->createOpenApiSchema(['type' => 'array']);

        $type = $this->extractor->extract($schema, $this->model, 'apiextensions.k8s.io', 'v1');

        $this->assertInstanceOf(Type::class, $type);
        $this->assertStringContainsString('array', (string) $type);
    }

    // Tests for error cases

    public function testExtractClassNameThrowsExceptionWhenSchemaMissing(): void
    {
        $openApiSchema = $this->createOpenApiSchemaWithPath(['components', 'schemas', 'io.k8s.api.core.v1.MissingSchema']);

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Unable to map type. Missing schema "io.k8s.api.core.v1.MissingSchema"');

        $this->extractor->extract($openApiSchema, $this->model, 'apiextensions.k8s.io', 'v1');
    }

    public function testExtractThrowsExceptionForUnknownType(): void
    {
        $schema = $this->createOpenApiSchema(['type' => 'unknown']);

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Unable to map type from "unknown"');

        $this->extractor->extract($schema, $this->model, 'apiextensions.k8s.io', 'v1');
    }

    public function testExtractThrowsExceptionWhenNoTypeInformation(): void
    {
        $schema = $this->createOpenApiSchema([]);

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Unable to convert type for field');

        $this->extractor->extract($schema, $this->model, 'apiextensions.k8s.io', 'v1');
    }

    /**
     * @param array<string, string> $data
     */
    private function createOpenApiSchema(array $data): OpenApiSchema
    {
        $schema = new OpenApiSchema($data);
        $schema->setDocumentContext(new OpenApi([]), new JsonPointer('/'));

        return $schema;
    }

    /**
     * @param string[] $path
     */
    private function createOpenApiSchemaWithPath(array $path): OpenApiSchema
    {
        $schema = new OpenApiSchema([]);
        $jsonPointer = new JsonPointer('/'.implode('/', $path));
        $schema->setDocumentContext(new OpenApi([]), $jsonPointer);

        return $schema;
    }

    public function getSchema(string $name): OpenApiSchema
    {
        $openApi = Reader::readFromJsonFile($this->fixturesPath.'/apiextensionsV1.json');

        /** @var ?OpenApiSchema $schema */
        $schema = $openApi->components?->schemas[$name];

        if (!$schema) {
            throw new \RuntimeException('Schema not found');
        }

        return $schema;
    }
}
