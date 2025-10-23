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

use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use P8p\CodeGenerator\Config\Api;
use P8p\CodeGenerator\Config\Config;
use P8p\CodeGenerator\Model\Model;
use P8p\CodeGenerator\Reader\OpenApiV3Reader;
use PHPUnit\Framework\TestCase;

class OpenApiV3ReaderTest extends TestCase
{
    private Config $config;
    private string $fixturesPath;

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
    }

    public function testResolvesSchemas(): void
    {
        $model = new Model();
        $this->loadModelFromFixtureFile($model);

        // Verify schemas are extracted
        $this->assertTrue($model->hasSchema('io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.CustomResourceDefinition'));
        $this->assertTrue($model->hasSchema('io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.CustomResourceColumnDefinition'));
        $this->assertTrue($model->hasSchema('io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.CustomResourceConversion'));

        // Verify schema has properties
        $crdSchema = $model->getSchema('io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.CustomResourceDefinition');
        $this->assertNotEmpty($crdSchema->getProperties());
        $this->assertTrue($crdSchema->hasProperty('apiVersion'));
        $this->assertTrue($crdSchema->hasProperty('kind'));
        $this->assertTrue($crdSchema->hasProperty('metadata'));
        $this->assertTrue($crdSchema->hasProperty('spec'));

        $metadata = $crdSchema->getClassMetadata();
        $this->assertEquals('P8p\Sdk\Schema\Apiextensions\V1\CustomResourceDefinition', $metadata->name);
        $this->assertEquals('/path/to/sdk/Schema/Apiextensions/V1/CustomResourceDefinition.php', $metadata->path);
    }

    public function testResolvesServices(): void
    {
        $model = new Model();
        $this->loadModelFromFixtureFile($model);

        $services = $model->getServices();
        $this->assertNotEmpty($services);

        $crdService = $model->getService('p8p.sdk.api.apiextensions.v1.customresourcedefinitionapi');

        // Verify service has operations
        $operations = $crdService->getOperations();
        $this->assertNotEmpty($operations);

        // Verify service has class metadata
        $metadata = $crdService->getClassMetadata();
        $this->assertEquals('P8p\Sdk\Api\Apiextensions\V1\CustomResourceDefinitionApi', $metadata->name);
        $this->assertEquals('/path/to/sdk/Api/Apiextensions/V1/CustomResourceDefinitionApi.php', $metadata->path);
    }

    public function testExtractedSchemasHaveCorrectTypes(): void
    {
        $model = new Model();
        $this->loadModelFromFixtureFile($model);

        $columnDefSchema = $model->getSchema('io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.CustomResourceColumnDefinition');

        // Check string properties
        $this->assertTrue($columnDefSchema->hasProperty('name'));
        $this->assertTrue($columnDefSchema->hasProperty('description'));
        $this->assertTrue($columnDefSchema->hasProperty('format'));

        // Check integer property
        $this->assertTrue($columnDefSchema->hasProperty('priority'));

        // Verify required vs optional properties (priority is optional, so should be nullable)
        $priorityProp = $columnDefSchema->getProperty('priority');
        $this->assertEquals('int|null', (string) $priorityProp->type);

        // name is required, should not be nullable
        $nameProp = $columnDefSchema->getProperty('name');
        $this->assertEquals('string', (string) $nameProp->type);
    }

    public function testSchemaPropertiesAreReorderedWithNullableLast(): void
    {
        $model = new Model();
        $this->loadModelFromFixtureFile($model);

        $crdSchema = $model->getSchema('io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.CustomResourceDefinition');
        $properties = array_values($crdSchema->getProperties());

        $this->assertEquals('spec', $properties[0]->name);
        $this->assertEquals('apiVersion', $properties[1]->name);
        $this->assertEquals('kind', $properties[2]->name);
        $this->assertEquals('metadata', $properties[3]->name);
        $this->assertEquals('status', $properties[4]->name);
    }

    private function loadModelFromFixtureFile(Model $model): void
    {
        $openApi = Reader::readFromJsonFile($this->fixturesPath.'/apiextensionsV1.json');

        // First resolve schemas (required for type extraction)
        $reader = new class('http://localhost', $this->config) extends OpenApiV3Reader {
            public function publicResolveSchemas(OpenApi $openApi, Model $model): void
            {
                $this->resolveSchemas($openApi, $model);
            }

            public function publicResolveService(OpenApi $openApi, Model $model): void
            {
                $this->resolveService($openApi, $model);
            }
        };

        $reader->publicResolveSchemas($openApi, $model);
        $reader->publicResolveService($openApi, $model);
    }
}
