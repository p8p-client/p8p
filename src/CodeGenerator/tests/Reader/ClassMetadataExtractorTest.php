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
use cebe\openapi\spec\Schema as OpenApiSchema;
use P8p\CodeGenerator\Config\Api;
use P8p\CodeGenerator\Config\Config;
use P8p\CodeGenerator\Model\ClassMetadata;
use P8p\CodeGenerator\Reader\ClassMetadataExtractor;
use PHPUnit\Framework\TestCase;

class ClassMetadataExtractorTest extends TestCase
{
    private ClassMetadataExtractor $extractor;
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
        $this->extractor = new ClassMetadataExtractor($this->config);
    }

    public function testExtractForSchema(): void
    {
        $openApi = Reader::readFromJsonFile($this->fixturesPath.'/apiextensionsV1.json');
        /** @var ?OpenApiSchema $crdSchema */
        $crdSchema = $openApi->components?->schemas['io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.CustomResourceDefinition'];
        $this->assertNotNull($crdSchema);

        $metadata = $this->extractor->extractForSchema($crdSchema);

        $this->assertEquals('P8p\Sdk\Schema\Apiextensions\V1\CustomResourceDefinition', $metadata->name);
        $this->assertEquals('/path/to/sdk/Schema/Apiextensions/V1/CustomResourceDefinition.php', $metadata->path);
    }

    public function testExtractForService(): void
    {
        $openApi = Reader::readFromJsonFile($this->fixturesPath.'/apiextensionsV1.json');

        // Get an operation with GVK
        $operation = null;
        foreach ($openApi->paths as $path => $pathItem) {
            foreach ($pathItem->getOperations() as $op) {
                if ('listApiextensionsV1CustomResourceDefinition' === $op->operationId) {
                    $operation = $op;
                    break 2;
                }
            }
        }

        $this->assertNotNull($operation, 'Should find an operation with GVK');

        $metadata = $this->extractor->extractForService($operation);

        $this->assertInstanceOf(ClassMetadata::class, $metadata);

        $this->assertEquals('P8p\Sdk\Api\Apiextensions\V1\CustomResourceDefinitionApi', $metadata->name);
        $this->assertEquals('/path/to/sdk/Api/Apiextensions/V1/CustomResourceDefinitionApi.php', $metadata->path);
    }
}
