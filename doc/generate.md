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
php generate.php https://my-cluster.example.com
```

### With a custom configuration file

```bash
php generate.php --config=/path/to/custom-config.php
# or
php generate.php https://my-cluster.example.com --config=/path/to/custom-config.php
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
        new Api('apiextensions.k8s.io', 'v1'),   // CRD API
        // Add more APIs as needed
    ],
    schemasOverride: [
        // Custom type overrides for your project (optional)
        // System overrides (IntOrString, Quantity, Time, etc.) are handled automatically
    ],
    documentationOutputDir: __DIR__.'/../../doc/sdk',  // Set to null to disable documentation
    externalSdkPath: null,  // Path to external SDK to avoid duplicating types (for CRD generation)
);
```

### Configuration Parameters

- **baseNamespace**: Target namespace for generated classes
- **basePath**: Output directory for generated classes
- **apis**: List of Kubernetes APIs to generate (group/version pairs)
- **schemasOverride**: Custom type overrides for project-specific needs (optional)
- **documentationOutputDir**: Output directory for documentation (optional, set to `null` to disable)
- **externalSdkPath**: Path to external SDK source directory (optional, for CRD generation)
- **documentationTemplateDir**: Custom templates directory (optional, defaults to built-in templates)

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

### System Overrides (Automatic)

The generator automatically handles special Kubernetes types that need custom PHP representations:

| Kubernetes Type | PHP Type | Description |
|----------------|----------|-------------|
| `IntOrString` | `int\|string` | Can be an integer or a string (e.g., port numbers) |
| `Quantity` | `int\|string` | Resource quantities (e.g., `"100Mi"`, `"2Gi"`) |
| `Time` / `MicroTime` | `\DateTime` | Kubernetes timestamps |
| `RawExtension` | `array\|object` | Arbitrary JSON data |
| `FieldsV1` / `Patch` | `array` | Arbitrary structures |
| `JSON*` types | `array` / `array\|bool` | JSON Schema types |

These overrides are **automatic** and do not need to be configured in `schemasOverride`.

### Custom Overrides

Use `schemasOverride` only for project-specific type mappings:

```php
schemasOverride: [
    'com.example.MyCustomType' => Type::string(),
],
```

## Generating Custom CRDs

P8P supports generating PHP classes for Custom Resource Definitions (CRDs) while reusing types from the main SDK.

See [Custom CRD Generation](./custom-crd.md) for detailed documentation on:
- Generating classes for your custom CRDs
- Integrating open-source packages (cert-manager, Prometheus Operator, Istio, ArgoCD, etc.)
