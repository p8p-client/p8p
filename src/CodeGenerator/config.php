<?php

use P8p\CodeGenerator\Config\Config;
use P8p\CodeGenerator\Config\Api;
use Symfony\Component\TypeInfo\Type;

return new Config(
    baseNamespace: 'P8p\\Sdk',
    basePath: __DIR__.'/../Sdk/src',
    apis: [
        new Api('', 'v1'),
        new Api('apps', 'v1'),
        new Api('apiextensions.k8s.io', 'v1')
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
);