# Generating Custom CRDs

This guide explains how to generate PHP classes for Custom Resource Definitions (CRDs) while reusing types from the main P8P SDK.

## Use Cases

- Generate classes for custom CRDs you've created
- Integrate open-source packages (cert-manager, Prometheus Operator, Istio, ArgoCD, etc.)

## Prerequisites

```bash
# In your project, install the P8P SDK
composer require p8p/sdk

# Install the generator in dev
composer require --dev p8p/generator
```

## Configuration

Create a configuration file (e.g., `config/k8s-generator.php`):

```php
<?php

use P8p\CodeGenerator\Config\Config;
use P8p\CodeGenerator\Config\Api;

return new Config(
    baseNamespace: 'App\\K8s',
    basePath: __DIR__.'/../src/K8s',
    apis: [
        new Api('cert-manager.io', 'v1'),
        new Api('mycompany.com', 'v1'),
    ],

    // Schema overrides (optional, for project-specific cases)
    // System overrides (IntOrString, Quantity, Time, etc.) are automatic!
    schemasOverride: [],

    // Documentation output (optional, set to null to disable)
    documentationOutputDir: __DIR__.'/../doc/k8s',

    // Reference SDK to avoid type duplication
    externalSdkPath: __DIR__.'/../vendor/p8p/sdk/src',
);
```

### Configuration Parameters

- **baseNamespace**: Target namespace for your generated classes
- **basePath**: Output directory for generated classes
- **apis**: List of CRD APIs to generate (group/version pairs)
- **schemasOverride**: Optional custom type overrides for project-specific needs
- **documentationOutputDir**: Optional documentation directory (set to `null` to disable)
- **externalSdkPath**: **Important**: Path to the P8P SDK source to avoid duplicating common types

## Generating Classes

```bash
# Start kubectl proxy (if needed)
kubectl proxy

# Generate classes
php vendor/bin/p8p-generate --config=config/k8s-generator.php

# Or specify a different API URL
php vendor/bin/p8p-generate https://my-k8s-cluster.com --config=config/k8s-generator.php
```

## Using Generated Classes

```php
<?php

use P8p\Client\ClientFactory;
use App\K8s\Api\CertManager\V1\CertificateApi;
use App\K8s\Schema\CertManager\V1\Certificate;
use App\K8s\Schema\CertManager\V1\CertificateSpec;
use App\K8s\Schema\CertManager\V1\IssuerRef;
use P8p\Sdk\Schema\Meta\V1\ObjectMeta; // Reuses SDK type!

// Create the client
$client = ClientFactory::fromKubeConfig('/path/to/kubeconfig')->getClient();

// Use the generated API
$certificateApi = $client->getApi(CertificateApi::class);

// Create a resource with mixed types
$certificate = new Certificate(
    metadata: new ObjectMeta(  // From P8P SDK
        name: 'my-cert',
        namespace: 'default',
    ),
    spec: [
        ...
    ]
]
);

$response = $certificateApi->create('default', $certificate);
```

