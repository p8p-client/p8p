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
    kind: 'ComplexResource',
    group: 'test.io',
    version: 'v1alpha1',
    plural: 'complexresources',
    namespaced: false,
)]
class ComplexCustomResource
{
    /**
     * @param array<string>        $tags
     * @param array<string, mixed> $config
     */
    public function __construct(
        public string $name,
        public int $priority,
        public float $threshold,
        public bool $active,
        public array $tags,
        public array $config,
        public ?string $optional = null,
    ) {
    }
}
