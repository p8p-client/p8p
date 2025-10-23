# Watching Resources

P8P allows you to monitor Kubernetes resources in real-time using the Kubernetes watch API.

## Basic Usage

To watch resources, pass the `watch` option to any list operation:

```php
use P8p\Client\ClientFactory;
use P8p\Sdk\Api\Core\V1\PodApi;
use P8p\Sdk\Schema\Core\V1\Pod;

// Create a client
$client = ClientFactory::fromUrl('http://127.0.0.1:8001')->getClient();
$podsApi = $client->getApi(PodApi::class);

// List pods with watch enabled
$response = $podsApi->list('default', [
    'watch' => true
]);

// Iterate over events
foreach ($response->watch(objectClass: Pod::class) as $event) {
    // Process each event
    dump($event);
}
```

## How it Works

When `watch` is enabled:

1. The list operation opens a persistent HTTP connection
2. Kubernetes streams events as resources change
3. The `watch()` method returns an iterator that produces events in real-time
4. Each event contains information about what changed (ADDED, MODIFIED, DELETED)


## Notes

- The `objectClass` parameter ensures correct deserialization of watched resources
- Watch works with any Kubernetes resource that supports the list operation
