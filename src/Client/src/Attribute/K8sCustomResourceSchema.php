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
 * Attribute to mark a PHP class as a Kubernetes Custom Resource.
 *
 * Usage:
 * #[CustomResource(
 *     group: 'example.com',
 *     version: 'v1',
 *     kind: 'MyResource',
 *     plural: 'myresources',
 *     namespaced: true
 * )]
 * class MyResource { ... }
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class K8sCustomResourceSchema extends K8sSchema
{
    public function __construct(
        public string $kind,
        public string $group,
        public string $version,
        public string $plural,
        public bool $namespaced = true,
        public ?string $singular = null,
        public ?string $shortName = null,
    ) {
        $this->singular ??= strtolower($kind);
        parent::__construct($kind, $group, $version);
    }

    public function getName(): string
    {
        return $this->plural.'.'.$this->group;
    }

    public function getScope(): string
    {
        return $this->namespaced ? 'Namespaced' : 'Cluster';
    }
}
