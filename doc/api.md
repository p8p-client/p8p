# Using Kubernetes APIs

This guide explains how to use Kubernetes APIs with P8P to interact with your cluster resources.

## getApi() Method

The P8P client provides a `getApi()` method that allows you to retrieve an API instance to interact with Kubernetes resources.

### Basic Usage

```php
use P8p\Client\ClientFactory;
use P8p\Sdk\Api\Core\V1\PodApi;

// Create the client
$client = ClientFactory::fromUrl('http://127.0.0.1:8001')->getClient();

// Get the Pod API
$podApi = $client->getApi(PodApi::class);

// Use the API
$pods = $podApi->list(namespace: 'default')->getContent();

dd($pods);
```

### How it Works

The `getApi()` method:
1. Takes the **API class** you want to use as a parameter
2. Returns a **configured instance** of this API
3. The instance is **ready to use** with the configured HTTP client


## Available APIs in the SDK

Most Kubernetes APIs are **automatically generated** and available in the `p8p/sdk` package.

> 📚 **Complete list**: Check [SDK - Available APIs](./sdk/index.md) to see all generated APIs.

## Usage Examples

### Managing Pods

```php
use P8p\Client\ClientFactory;
use P8p\Sdk\Api\Core\V1\PodApi;
use P8p\Sdk\Schema\Core\V1\Pod;
use P8p\Sdk\Schema\Meta\V1\ObjectMeta;
use P8p\Sdk\Schema\Core\V1\PodSpec;
use P8p\Sdk\Schema\Core\V1\Container;
use P8p\Sdk\Schema\Core\V1\ContainerPort;
use P8p\Sdk\Schema\Meta\V1\DeleteOptions;

$client = ClientFactory::fromUrl('http://127.0.0.1:8001')->getClient();
$podApi = $client->getApi(PodApi::class);

// List pods
$pods = $podApi->list(namespace: 'default')->getContent();
foreach ($pods->items as $pod) {
    echo $pod->metadata->name . "\n";
}

// Read a specific pod
$pod = $podApi->read(name: 'my-pod', namespace: 'default');
echo $pod->status->phase . "\n";

// Create a pod
$pod = $podApi->create('default', new Pod(
    metadata: new ObjectMeta(
        name: 'nginx-pod',
    ),
    spec: new PodSpec(
        containers: [
            new Container(
                name: 'nginx',
                image: 'nginx',
                ports: [
                    new ContainerPort(
                        containerPort: 80,
                    )
                ],
            )
        ],
    )
))->getContent();

// Delete a pod
$podApi->delete(name: 'nginx-pod', namespace: 'default', body: new DeleteOptions());
```

## Error Handling

When calling the API, the response returned by the client allows you to check the success of the request using the `isSuccessful()` method.
If this method returns `false`, you can then use `getError()` to retrieve a **Status** object containing detailed information about the error cause.

```php
use P8p\Client\ClientFactory;
use P8p\Sdk\Api\Core\V1\PodApi;

$client = ClientFactory::fromUrl('http://127.0.0.1:8001')->getClient();
$podApi = $client->getApi(PodApi::class);
$rs = $podApi->readLog('my-pod', 'default');

if(!$rs->isSuccessful()) {
    dd($rs->getError());
}

dd($rs->getContent());
```

## WebSocket Operations

Some API operations do **not** return simple DTOs, but **WebSocket connections**.
This is notably the case for the following methods:

- `Exec`
- `Proxy`
- `Attach`
- `Forward`

In this case you receive a `P8p\Client\WebSocket\WebSocketConnection` object that allows you to interact with the API.

## Advanced Features

### Watch (change monitoring)

See the [Watch](./watch.md) guide to monitor changes in real-time.

### Exec (command execution)

See the [Exec](./exec.md) guide to execute commands in containers.


