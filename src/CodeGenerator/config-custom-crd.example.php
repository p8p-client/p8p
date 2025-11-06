<?php

/**
 * Example configuration for generating Custom Resource Definitions (CRDs).
 *
 * This configuration demonstrates how to generate PHP classes for custom Kubernetes APIs
 * while referencing the standard P8P SDK to avoid duplicating common types like ObjectMeta.
 *
 * Usage:
 *   php generate.php --config=config-custom-crd.example.php
 *
 * Prerequisites:
 *   - The P8P SDK package must be installed: composer require p8p/sdk
 *   - Kubernetes API server must be accessible (or use kubectl proxy)
 *   - The CRDs must be installed on the cluster
 */

use P8p\CodeGenerator\Config\Config;
use P8p\CodeGenerator\Config\Api;

return new Config(
    // Target namespace for generated classes
    baseNamespace: 'App\\K8s',

    // Output directory for generated classes
    basePath: __DIR__.'/tmp',

    // List of APIs to generate
    // Replace these with your actual CRD groups and versions
    apis: [
        // Example: cert-manager CRDs
        new Api('cert-manager.io', 'v1'),

        // Example: Prometheus Operator CRDs
        // new Api('monitoring.coreos.com', 'v1'),

        // Example: Argo CD CRDs
        // new Api('argoproj.io', 'v1alpha1'),

        // Example: Istio CRDs
        // new Api('networking.istio.io', 'v1beta1'),
        // new Api('security.istio.io', 'v1beta1'),

        // Example: Custom internal CRDs
        // new Api('mycompany.com', 'v1'),
    ],

    // Schema overrides (optional, for custom project-specific type mappings)
    // System overrides (IntOrString, Quantity, Time, etc.) are automatically handled.

    // Documentation output (optional)
    documentationOutputDir: __DIR__.'/../../doc/custom-k8s',
    externalSdkPath: __DIR__.'/../Sdk/src',
);
