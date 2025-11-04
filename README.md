# P8P - PHP Client for Kubernetes

P8P is a PHP SDK for interacting with Kubernetes APIs. It automatically generates strongly-typed PHP classes from Kubernetes OpenAPI specifications.

## 📚 Documentation

Check out the [complete documentation](./doc/index.md) to get started with P8P.

## 📦 Packages

This project is organized as a monorepo. Each package is also available individually:

- **[p8p/client](https://github.com/p8p-client/client)** - HTTP client for communicating with Kubernetes APIs
- **[p8p/symfony-bundle](https://github.com/p8p-client/symfony-bundle)** - Symfony bundle for easy integration
- **[p8p/generator](https://github.com/p8p-client/generator)** - Code generator from OpenAPI specifications
- **[p8p/sdk](https://github.com/p8p-client/sdk)** - Generated Kubernetes SDK (models and services)

## 🚀 Quick Example

```php
use P8p\Client\ClientFactory;
use P8p\Sdk\Api\Core\V1\PodApi;

$client = ClientFactory::fromUrl('http://127.0.0.1:8001')->getClient();

$podApi = $client->getApi(PodApi::class);
$pods = $podApi->list(namespace: 'default')->getContent();

dd($pods);
```

