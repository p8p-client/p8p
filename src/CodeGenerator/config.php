<?php

use P8p\CodeGenerator\Config\Config;
use P8p\CodeGenerator\Config\Api;
use Symfony\Component\TypeInfo\Type;

return new Config(
    baseNamespace: 'P8p\\Sdk',
    basePath: __DIR__.'/../Sdk/src',
    apis: [
        new Api('', 'v1'),
        new Api('admissionregistration.k8s.io', 'v1'),
        new Api('apiextensions.k8s.io', 'v1'),
        new Api('apiregistration.k8s.io', 'v1'),
        new Api('apps', 'v1'),
        new Api('authentication.k8s.io', 'v1'),
        new Api('authorization.k8s.io', 'v1'),
        new Api('autoscaling', 'v1'),
        new Api('autoscaling', 'v2'),
        new Api('batch', 'v1'),
        new Api('certificates.k8s.io', 'v1'),
        new Api('coordination.k8s.io', 'v1'),
        new Api('discovery.k8s.io', 'v1'),
        new Api('events.k8s.io', 'v1'),
        new Api('flowcontrol.apiserver.k8s.io', 'v1'),
        new Api('networking.k8s.io', 'v1'),
        new Api('node.k8s.io', 'v1'),
        new Api('policy', 'v1'),
        new Api('rbac.authorization.k8s.io', 'v1'),
        new Api('scheduling.k8s.io', 'v1'),
        new Api('storage.k8s.io', 'v1'),
    ],
    schemasOverride: [
        // System overrides (IntOrString, Quantity, Time, etc.) are now handled automatically by TypeExtractor.
        // Only add custom project-specific overrides here if needed.
        // 'io.k8s......' => Type::array(),
    ],
    documentationOutputDir: __DIR__ . '/../../doc/sdk',
    externalSdkPath: null,
);