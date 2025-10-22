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

use P8p\CodeGenerator\Model\ClassMetadata;
use PHPUnit\Framework\TestCase;

class ClassMetadataTest extends TestCase
{
    public function testConstructor(): void
    {
        $metadata = new ClassMetadata('App\\Model\\User', '/path/to/User.php');

        $this->assertSame('App\\Model\\User', $metadata->name);
        $this->assertSame('/path/to/User.php', $metadata->path);
    }

    public function testGetShortName(): void
    {
        $metadata = new ClassMetadata('App\\Model\\User', '/path/to/User.php');

        $this->assertSame('User', $metadata->getShortName());
    }

    public function testGetShortNameWithSingleClass(): void
    {
        $metadata = new ClassMetadata('User', '/path/to/User.php');

        $this->assertSame('User', $metadata->getShortName());
    }

    public function testGetNamespace(): void
    {
        $metadata = new ClassMetadata('App\\Model\\User', '/path/to/User.php');

        $this->assertSame('App\\Model', $metadata->getNamespace());
    }

    public function testGetNamespaceWithSingleClass(): void
    {
        $metadata = new ClassMetadata('User', '/path/to/User.php');

        $this->assertSame('', $metadata->getNamespace());
    }

    public function testWithKubernetesStyleClassName(): void
    {
        $metadata = new ClassMetadata(
            'P8p\\Sdk\\Schema\\Apiextensions\\V1\\CustomResourceDefinition',
            '/path/to/CustomResourceDefinition.php'
        );

        $this->assertSame('CustomResourceDefinition', $metadata->getShortName());
        $this->assertSame('P8p\\Sdk\\Schema\\Apiextensions\\V1', $metadata->getNamespace());
        $this->assertSame('P8p\\Sdk\\Schema\\Apiextensions\\V1\\CustomResourceDefinition', $metadata->name);
        $this->assertSame('/path/to/CustomResourceDefinition.php', $metadata->path);
    }
}
