<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Tests\Attribute;

use P8p\Client\Attribute\K8sSchema;
use P8p\Client\Tests\Fixtures\Schema\SimplePod;
use P8p\Client\Tests\Fixtures\Schema\TestClass;
use PHPUnit\Framework\TestCase;

class K8sSchemaTest extends TestCase
{
    public function testCoreSchema(): void
    {
        $reflection = new \ReflectionClass(TestClass::class);
        $attributes = $reflection->getAttributes(K8sSchema::class);

        $this->assertCount(1, $attributes);

        /** @var K8sSchema $attribute */
        $attribute = $attributes[0]->newInstance();
        $this->assertSame('Deployment', $attribute->kind);
        $this->assertSame('apps', $attribute->group);
        $this->assertSame('v1', $attribute->version);
        $this->assertSame('apps/v1', $attribute->getApiVersion());
    }

    public function testGroupSchema(): void
    {
        $reflection = new \ReflectionClass(SimplePod::class);
        $attributes = $reflection->getAttributes(K8sSchema::class);

        $this->assertCount(1, $attributes);

        /** @var K8sSchema $attribute */
        $attribute = $attributes[0]->newInstance();
        $this->assertSame('Pod', $attribute->kind);
        $this->assertSame('', $attribute->group);
        $this->assertSame('v1', $attribute->version);
        $this->assertSame('v1', $attribute->getApiVersion());
    }
}
