# Installation and Configuration

This guide will help you install P8P.

## Prerequisites

- **PHP 8.4 or higher**
- **Composer** (PHP dependency manager)
- **Access to a Kubernetes cluster**

## Installation via Composer

To install the client and the pre-generated SDK in your project:

```bash
composer require p8p/sdk
```

If you only want to install the client:

```bash
composer require p8p/client
```

If you want to generate your own SDK:

```bash
composer require p8p/generator
```

## Kubernetes Cluster Configuration

P8P requires access to the Kubernetes API to function.

### Option 1: kubectl proxy (local development)

The simplest method for development:

```bash
kubectl proxy
```

This exposes the Kubernetes API on `http://127.0.0.1:8001/` without authentication.

The proxy method does not expose, by default, certain APIs like '/exec' methods.
If you need them, you will need to run the following command:

```bash
kubectl proxy --reject-paths ""
```

You can then create a client with the factory:

```php
$client = ClientFactory::fromUrl('http://127.0.0.1:8001')->getClient();;
```

### Option 2: In-Cluster (Automatic Service Account)

For applications running in a Kubernetes Pod:

```php
use P8p\Client\ClientFactory;
use P8p\Client\Credentials\InClusterCredentials;

$client = ClientFactory::fromInClusterConfiguration()->getClient();
```

This method automatically reads:
- The service account token mounted in `/var/run/secrets/kubernetes.io/serviceaccount/token`
- The CA certificate in `/var/run/secrets/kubernetes.io/serviceaccount/ca.crt`


### Option 3: KubeConfig File

This is the standard configuration file used by `kubectl` (usually located at `~/.kube/config`).

Load credentials from a kubeconfig file:

```php
use P8p\Client\ClientFactory;

// Use default context from kubeconfig
$client = ClientFactory::fromKubeConfig('/path/to/kubeconfig')->getClient();

// Use specific context
$client = ClientFactory::fromKubeConfig(
    path: '/path/to/kubeconfig',
    context: 'my-context'
)->getClient();
```

**Note**: KubeConfig support requires the `symfony/yaml` package:

```bash
composer require symfony/yaml
```

### Option 4: Custom Auth

For more specific needs you can use the different options of `ClientFactory::fromUrl`

```php
$client = ClientFactory::fromUrl(
    endpoint: string,
    token: ?string,
    caFile: ?string,
    certificationFile: ?string,
    privateKeyFile: ?string,
    httpUser: ?string,
    httpPassword: ?string,
)->getClient();;
```



