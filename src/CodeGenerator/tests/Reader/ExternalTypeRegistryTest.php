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

use P8p\CodeGenerator\Reader\ExternalTypeRegistry;
use PHPUnit\Framework\TestCase;

class ExternalTypeRegistryTest extends TestCase
{
    private ExternalTypeRegistry $registry;
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->registry = new ExternalTypeRegistry();
        $this->fixturesPath = __DIR__.'/../Fixtures/external-sdk';
    }

    public function testScanDiscoversTypesWithK8sSchemaRefAttribute(): void
    {
        $this->registry->scan($this->fixturesPath);

        // Should find Pod schema
        $this->assertTrue($this->registry->hasSchema('io.k8s.api.core.v1.Pod'));
        $this->assertEquals(
            'P8p\\Sdk\\Schema\\Core\\V1\\TestPod',
            $this->registry->resolveSchemaName('io.k8s.api.core.v1.Pod')
        );

        // Should find ObjectMeta schema
        $this->assertTrue($this->registry->hasSchema('io.k8s.apimachinery.pkg.apis.meta.v1.ObjectMeta'));
        $this->assertEquals(
            'P8p\\Sdk\\Schema\\Meta\\V1\\TestObjectMeta',
            $this->registry->resolveSchemaName('io.k8s.apimachinery.pkg.apis.meta.v1.ObjectMeta')
        );

        // Should find TypeMeta schema
        $this->assertTrue($this->registry->hasSchema('io.k8s.apimachinery.pkg.apis.meta.v1.TypeMeta'));
        $this->assertEquals(
            'P8p\\Sdk\\Schema\\Meta\\V1\\TypeMeta',
            $this->registry->resolveSchemaName('io.k8s.apimachinery.pkg.apis.meta.v1.TypeMeta')
        );
    }

    public function testScanIgnoresClassesWithoutK8sSchemaRefAttribute(): void
    {
        $this->registry->scan($this->fixturesPath);

        // NoAttribute class should not be registered
        $this->assertFalse($this->registry->hasSchema('NoAttribute'));
        $this->assertNull($this->registry->resolveSchemaName('NoAttribute'));
    }

    public function testResolveSchemaNameReturnsNullForUnknownSchema(): void
    {
        $this->registry->scan($this->fixturesPath);

        $this->assertFalse($this->registry->hasSchema('io.k8s.unknown.Schema'));
        $this->assertNull($this->registry->resolveSchemaName('io.k8s.unknown.Schema'));
    }

    public function testHasSchemaReturnsFalseBeforeScan(): void
    {
        // Don't scan - registry should be empty
        $this->assertFalse($this->registry->hasSchema('io.k8s.api.core.v1.Pod'));
    }

    public function testScanThrowsExceptionForNonExistentDirectory(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Schema directory "/non/existent/path/Schema" does not exist');

        $this->registry->scan('/non/existent/path');
    }

    public function testScanHandlesEmptyDirectory(): void
    {
        $emptyDir = sys_get_temp_dir().'/p8p-test-empty-'.uniqid();
        mkdir($emptyDir);
        mkdir($emptyDir.'/Schema');

        try {
            $this->registry->scan($emptyDir);
            $this->assertFalse($this->registry->hasSchema('anything'));
        } finally {
            rmdir($emptyDir.'/Schema');
            rmdir($emptyDir);
        }
    }
}
