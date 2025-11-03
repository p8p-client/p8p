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
    kind: 'TestResource',
    group: 'example.com',
    version: 'v1',
    plural: 'testresources',
    namespaced: true,
)]
class TestCustomResource
{
    /**
     * @param array<string, mixed>|null $spec
     */
    public function __construct(
        public ?string $name = null,
        public ?array $spec = null,
    ) {
    }
}
