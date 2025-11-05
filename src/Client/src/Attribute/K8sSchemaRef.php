<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Attribute;

/**
 * Technical attribute that stores the OpenAPI schema reference name.
 * Example:
 *   #[K8sSchemaRef('io.k8s.apimachinery.pkg.apis.meta.v1.ObjectMeta')]
 *   class ObjectMeta { ... }.
 */
#[\Attribute]
class K8sSchemaRef
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}
