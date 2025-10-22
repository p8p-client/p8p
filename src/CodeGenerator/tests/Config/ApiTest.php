<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Tests\Config;

use P8p\CodeGenerator\Config\Api;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    public function testConstructorSetsGroupAndVersion(): void
    {
        $api = new Api('apps', 'v1');

        $this->assertSame('apps', $api->group);
        $this->assertSame('v1', $api->version);
    }

    public function testIsCoreReturnsTrueForEmptyGroup(): void
    {
        $api = new Api('', 'v1');

        $this->assertTrue($api->isCore());
    }

    public function testIsCoreReturnsFalseForGroupedApi(): void
    {
        $api = new Api('apps', 'v1');

        $this->assertFalse($api->isCore());
    }

    public function testIsCoreReturnsFalseForApiextensions(): void
    {
        $api = new Api('apiextensions.k8s.io', 'v1');

        $this->assertFalse($api->isCore());
    }
}
