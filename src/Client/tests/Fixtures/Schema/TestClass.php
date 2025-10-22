<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Tests\Fixtures\Schema;

use P8p\Client\Attribute\K8sSchema;

#[K8sSchema(kind: 'Deployment', apiVersion: 'apps/v1')]
class TestClass
{
}
