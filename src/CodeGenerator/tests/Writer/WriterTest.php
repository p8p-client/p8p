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
use P8p\CodeGenerator\Model\Model;
use P8p\CodeGenerator\Model\Operation;
use P8p\CodeGenerator\Model\Property;
use P8p\CodeGenerator\Model\Schema;
use P8p\CodeGenerator\Model\Service;
use P8p\CodeGenerator\Model\VerbEnum;
use P8p\CodeGenerator\Writer\Writer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

class WriterTest extends TestCase
{
    private string $tempDir;
    private Writer $writer;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir().'/p8p_writer_test_'.uniqid();
        mkdir($this->tempDir, 0777, true);
        $this->writer = new Writer();
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            $this->removeDirectory($this->tempDir);
        }
    }

    public function testWriteSchema(): void
    {
        $model = new Model();

        $schema = new Schema('io.k8s.api.core.v1.Pod');
        $schemaPath = $this->tempDir.'/Schema/Core/V1/Pod.php';
        $schema->setClassMetadata(new ClassMetadata('App\\Schema\\Core\\V1\\Pod', $schemaPath));
        $schema->addProperty(new Property('name', 'Pod name', Type::string()));

        $model->addSchema($schema);

        $this->writer->write($model);

        $this->assertFileExists($schemaPath);

        $content = file_get_contents($schemaPath);
        $this->assertNotFalse($content);
        $this->assertStringContainsString('namespace App\\Schema\\Core\\V1;', $content);
        $this->assertStringContainsString('class Pod', $content);
        $this->assertStringContainsString('public function __construct', $content);
        $this->assertStringContainsString('public string $name', $content);
    }

    public function testWriteService(): void
    {
        $model = new Model();

        $service = new Service('core.v1.pod');
        $servicePath = $this->tempDir.'/Api/Core/V1/PodApi.php';
        $service->setClassMetadata(new ClassMetadata('App\\Api\\Core\\V1\\PodApi', $servicePath));

        $operation = new Operation(
            operationId: 'listPods',
            name: 'list',
            path: '/api/v1/pods',
            verb: VerbEnum::GET,
            groupVersionKind: new GroupVersionKind('', 'v1', 'Pod'),
            type: 'list'
        );
        $service->addOperation($operation);

        $model->addService($service);

        $this->writer->write($model);

        $this->assertFileExists($servicePath);

        $content = file_get_contents($servicePath);
        $this->assertNotFalse($content);
        $this->assertStringContainsString('namespace App\\Api\\Core\\V1;', $content);
        $this->assertStringContainsString('class PodApi', $content);
        $this->assertStringContainsString('extends AbstractApi', $content);
        $this->assertStringContainsString('public function list', $content);
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir.'/'.$file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
