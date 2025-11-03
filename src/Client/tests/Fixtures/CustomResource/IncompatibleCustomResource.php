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
    kind: 'DifferentKind',
    group: 'example.com',
    version: 'v2',
    plural: 'differentresources',
    namespaced: true,
)]
class IncompatibleCustomResource
{
    public function __construct(
        public string $name,
    ) {
    }
}
