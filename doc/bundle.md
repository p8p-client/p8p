# Symfony Bundle

P8P provides a Symfony bundle to simplify integration with Symfony applications. The bundle handles client configuration, dependency injection, and provides a registry for managing multiple Kubernetes clients.

## Installation

Install the bundle via Composer:

```bash
composer require p8p/symfony-bundle
```

If you're using Symfony Flex, the bundle will be automatically registered. Otherwise, add it to `config/bundles.php`:

```php
return [
    // ...
    P8p\Bundle\P8pBundle::class => ['all' => true],
];
```

## Configuration

The bundle is configured using DSN (Data Source Name) strings that define how to connect to Kubernetes clusters.

### Basic Configuration

Create a configuration file in `config/packages/p8p.yaml`:

```yaml
p8p:
    clients:
        default:
            dsn: '%env(KUBERNETES_DSN)%'
```

Then define your DSN in your `.env` file:

```bash
KUBERNETES_DSN="kube://in-cluster"
```

### DSN Formats

The bundle supports three types of DSN:

#### 1. In-Cluster Configuration

For applications running inside Kubernetes pods:

```bash
KUBERNETES_DSN="kube://in-cluster"
```

This automatically reads:
- The service account token from `/var/run/secrets/kubernetes.io/serviceaccount/token`
- The CA certificate from `/var/run/secrets/kubernetes.io/serviceaccount/ca.crt`

#### 2. KubeConfig File

For using kubectl configuration files:

```bash
KUBERNETES_DSN="kube://kubeconfig?path=/home/user/.kube/config"
```

Optional parameters:
- `context` - Specific context name (uses default context if omitted)

Example with specific context:

```bash
KUBERNETES_DSN="kube://kubeconfig?path=/home/user/.kube/config&context=my-context"
```

**Note**: Requires the `symfony/yaml` package:

```bash
composer require symfony/yaml
```

#### 3. HTTP Connection

For direct HTTP connections (e.g., kubectl proxy):

```bash
KUBERNETES_DSN="kube://http?endpoint=http://127.0.0.1:8001"
```

Optional parameters:
- `token` - Authentication token (file path or value)
- `ca` - CA certificate file path
- `cert` - Client certificate file path
- `key` - Client private key file path

Example with authentication:

```bash
KUBERNETES_DSN="kube://http?endpoint=https://kubernetes.default.svc&token=/var/run/secrets/token&ca=/var/run/secrets/ca.crt"
```

### Multiple Clients

You can configure multiple Kubernetes clients:

```yaml
# config/packages/p8p.yaml
p8p:
    clients:
        default:
            dsn: '%env(KUBERNETES_PRIMARY_DSN)%'
        secondary:
            dsn: '%env(KUBERNETES_SECONDARY_DSN)%'
    default_client: default
```

```bash
# .env
KUBERNETES_PRIMARY_DSN="kube://in-cluster"
KUBERNETES_SECONDARY_DSN="kube://kubeconfig?path=/path/to/other/config"
```

## Usage

### Basic Usage (Default Client)

The default client is automatically available for dependency injection:

```php
use P8p\Client\Client;
use P8p\Sdk\Api\Core\V1\PodApi;

class MyService
{
    public function __construct(
        private Client $client,
    ) {
    }

    public function listPods(): void
    {
        $podApi = $this->client->getApi(PodApi::class);
        $pods = $podApi->list(namespace: 'default')->getContent();

        foreach ($pods->items as $pod) {
            echo $pod->metadata?->name . "\n";
        }
    }
}
```

### Named Client Injection

You can inject specific clients by name using the autowiring variable name convention:

```php
use P8p\Client\Client;

class MyService
{
    public function __construct(
        private Client $defaultClient,    // Injects the 'default' client
        private Client $secondaryClient,  // Injects the 'secondary' client
    ) {
    }
}
```

The bundle automatically creates autowiring aliases by:
1. Converting snake_case to camelCase
2. Appending "Client"

Examples:
- `default` → `$defaultClient`
- `secondary` → `$secondaryClient`
- `my_cluster` → `$myClusterClient`

### Using the ClientRegistry

For dynamic client selection at runtime, use the `ClientRegistry`:

```php
use P8p\Bundle\Factory\ClientRegistry;
use P8p\Sdk\Api\Core\V1\PodApi;

class MyService
{
    public function __construct(
        private ClientRegistry $clientRegistry,
    ) {
    }

    public function listPodsFromCluster(string $clusterName): void
    {
        $client = $this->clientRegistry->get($clusterName);
        $podApi = $client->getApi(PodApi::class);
        $pods = $podApi->list(namespace: 'default')->getContent();

        // Process pods...
    }

    public function listPodsFromDefaultCluster(): void
    {
        $client = $this->clientRegistry->getDefault();
        // Use the default client...
    }

    public function listAllClusters(): array
    {
        return $this->clientRegistry->getClientNames();
    }
}
```

## Controller Example

```php
use P8p\Client\Client;
use P8p\Sdk\Api\Core\V1\PodApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class KubernetesController extends AbstractController
{
    #[Route('/pods/{namespace}', name: 'list_pods')]
    public function listPods(string $namespace, Client $client): JsonResponse
    {
        $podApi = $client->getApi(PodApi::class);
        $pods = $podApi->list(namespace: $namespace)->getContent();

        $podNames = array_map(
            fn($pod) => $pod->metadata->name,
            $pods->items
        );

        return $this->json($podNames);
    }
}
```

## Going Further

For complete API documentation and advanced usage:

- **[SDK Documentation](./sdk/index.md)** - Complete reference for all available Kubernetes APIs
- **[Watch API](./watch.md)** - Listen to real-time changes in Kubernetes resources
- **[Exec Helper](./exec.md)** - Execute commands inside pods
