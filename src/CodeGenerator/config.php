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
        'io.k8s.apimachinery.pkg.util.intstr.IntOrString' => Type::union(Type::int(), Type::string()),
        'io.k8s.apimachinery.pkg.api.resource.Quantity' => Type::union(Type::int(), Type::string()),
        'io.k8s.apimachinery.pkg.runtime.RawExtension' => Type::union(Type::array(), Type::object()),
        'io.k8s.apimachinery.pkg.apis.meta.v1.Time' => Type::object(\DateTime::class),
        'io.k8s.apimachinery.pkg.apis.meta.v1.MicroTime' => Type::object(\DateTime::class),
        'io.k8s.apimachinery.pkg.apis.meta.v1.FieldsV1' => Type::array(),
        'io.k8s.apimachinery.pkg.apis.meta.v1.Patch' => Type::array(),
        'io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.CustomResourceSubresourceStatus' => Type::array(),
        'io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.JSON' => Type::array(),
        'io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.JSONSchemaProps' => Type::array(),
        'io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.JSONSchemaPropsOrBool' => Type::union(Type::array(), Type::bool()),
        'io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.JSONSchemaPropsOrStringArray' => Type::array(),
        'io.k8s.apiextensions-apiserver.pkg.apis.apiextensions.v1.JSONSchemaPropsOrArray' => Type::array(),
    ],
    documentationOutputDir: __DIR__ . '/../../doc/sdk',
    documentationTemplateDir: __DIR__ . '/templates/documentation',
);