<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Tests\Fixtures\CustomResource;

use P8p\Client\Attribute\K8sCustomResourceSchema;

#[K8sCustomResourceSchema(
    kind: 'SimpleResource',
    group: 'example.com',
    version: 'v1',
    plural: 'simpleresources',
    namespaced: true,
    singular: 'simpleresource',
    shortName: 'sr',
)]
class SimpleCustomResource
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public int $replicas = 1,
    ) {
    }
}
