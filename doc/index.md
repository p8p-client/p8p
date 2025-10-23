# P8P - PHP Client for Kubernetes

P8P is a PHP SDK for interacting with Kubernetes APIs.
It automatically generates strongly-typed PHP classes from Kubernetes OpenAPI specifications, providing a modern development experience with full auto-completion support.

## 🎯 Key Features

- **Automatic generation**: Creates models and services from OpenAPI v3 specs
- **Modern HTTP client**: Based on Symfony HttpClient with Kubernetes authentication support
- **WebSocket client**: Native [AMPHP](https://amphp.org/) integration
- **Strong typing**: PHP 8.4+ classes with typed properties

## 📚 Documentation

### Getting Started

- [Installation and Configuration](./installation.md) - Installation
- [Using Kubernetes APIs](./api.md) - Make your first calls
- [SDK](./sdk/index.md) - List of available APIs

### Advanced

- [Watch](./watch.md) - Listen to changes
- [Exec](./exec.md) - Use the ExecHelper

### Contributors
Guides for contributing to the project

- [Contributing](./contributing.md) - Project contribution guide
- [Project Architecture](./architecture.md) - Technical overview
- [Generate the SDK](./generate.md) - Launch code generation


## 🚀 Quick Example

```php
use P8p\Client\ClientFactory;
use P8p\Sdk\Api\Core\V1\PodApi;

$client = ClientFactory::fromUrl('http://127.0.0.1:8001')->getClient();;

$podApi = $client->getApi(PodApi::class);
$pods = $podApi->list(namespace: 'default');

dd($pods->getContent())
```
