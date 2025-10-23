# Generating the Kubernetes SDK

P8P automatically generates PHP classes for Kubernetes resources and APIs from your cluster's OpenAPI v3 specifications.

## Basic Usage

Launch generation from the `CodeGenerator` directory:

```bash
cd src/CodeGenerator
php generate.php
```

By default, the generator connects to `http://127.0.0.1:8001/` (kubectl proxy).

## Connecting to the Cluster

### With kubectl proxy (recommended)

```bash
# In one terminal, start the proxy
kubectl proxy

# In another terminal, launch generation
cd src/CodeGenerator
php generate.php
```

### With a custom URL

```bash
php generate.php --base-url=https://my-cluster.example.com
```

### With a custom configuration file

```bash
php generate.php --config=/path/to/custom-config.php
```

## Configuration

The `src/CodeGenerator/config.php` file defines what will be generated:

```php
return new Config(
    baseNamespace: 'P8p\\Sdk',
    basePath: __DIR__.'/../Sdk/src',
    apis: [
        new Api('', 'v1'),                        // Core API (Pod, Service, ConfigMap...)
        new Api('apps', 'v1'),                    // Apps API (Deployment, StatefulSet...)
        new Api('apiextensions.k8s.io', 'v1')    // CRD API
    ],
    schemasOverride: [
        // Type overrides for special Kubernetes types
        'io.k8s.apimachinery.pkg.util.intstr.IntOrString' => Type::union(Type::int(), Type::string()),
        // ...
    ],
);
```

## Generation Output

Classes are generated in `src/Sdk/src/`:

```
src/Sdk/src/
├── Api/           # API classes for interacting with Kubernetes
│   ├── Core/
│   │   └── V1/
│   │       ├── PodApi.php
│   │       ├── ServiceApi.php
│   │       └── ...
│   └── Apps/
│       └── V1/
│           ├── DeploymentApi.php
│           └── ...
└── Schema/        # Model classes for resources
    ├── Core/
    │   └── V1/
    │       ├── Pod.php
    │       ├── Service.php
    │       └── ...
    └── Apps/
        └── V1/
            ├── Deployment.php
            └── ...
```

## How it Works

1. The generator fetches OpenAPI v3 specifications from the Kubernetes cluster
2. It analyzes schemas and extracts metadata (Group/Version/Kind)
3. It generates PHP classes with types and properties
4. API classes contain CRUD methods (create, read, update, delete, list, watch)
5. Schema classes represent resources with their typed properties

## Type Overrides

Some Kubernetes types require special handling via `schemasOverride`:

- **Quantity**: Resource representation (`"100Mi"`, `"2Gi"`)
- **Time**: Converted to PHP `DateTime` object
- **RawExtension**: Arbitrary JSON data
- ...
