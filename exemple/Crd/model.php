<?php

namespace P8p;

use P8p\Client\Attribute\K8sCustomResourceSchema;
use P8p\Client\CustomResource\AbstractCustomResource;
use P8p\Client\CustomResource\CustomResourceInterface;
use P8p\Sdk\Schema\Meta\V1\ObjectMeta;

/**
 * CronTab custom resource - Version 1.
 */
#[K8sCustomResourceSchema(
    kind: 'CronTab',
    group: 'stable.example.com',
    version: 'v1',
    plural: 'crontabs',
    namespaced: true,
    singular: 'crontab',
    shortName: 'ct'
)]
class CronTabV1 implements CustomResourceInterface
{
    public function __construct(
        public ObjectMeta $metadata,
        public CronTabSpecV1 $spec,
    ) {
    }
}

class CronTabSpecV1
{
    public function __construct(
        public string $cronSpec,
        public string $image,
        public ?int $replicas = 1,
    ) {
    }
}

/**
 * CronTab custom resource - Version 2 (with command field).
 */
#[K8sCustomResourceSchema(
    kind: 'CronTab',
    group: 'stable.example.com',
    version: 'v2',
    plural: 'crontabs',
    namespaced: true,
    singular: 'crontab',
    shortName: 'ct'
)]
class CronTabV2 implements CustomResourceInterface
{
    public function __construct(
        public ObjectMeta $metadata,
        public CronTabSpecV2 $spec,
    ) {
    }
}

class CronTabSpecV2
{
    public function __construct(
        public string $cronSpec,
        public string $image,
        public ?int $replicas = 1,
        public ?string $command = null,
    ) {
    }
}