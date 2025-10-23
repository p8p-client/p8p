# Executing Commands in Pods

P8P allows you to execute commands in Kubernetes pods via WebSocket using the Kubernetes exec API.

## Basic Usage

To execute a command in a pod, use the `PodExecOptionsApi` API and the `ExecHelper` helper:

```php
use P8p\Client\ClientFactory;
use P8p\Client\Helper\ExecHelper;
use P8p\Sdk\Api\Core\V1\PodExecOptionsApi;

// Create a client
$client = ClientFactory::fromUrl('http://127.0.0.1:8001')->getClient();
$podExecApi = $client->getApi(PodExecOptionsApi::class);

// Establish a WebSocket connection with the pod
$connection = $podExecApi->connectPostPodExec(
    name: 'my-pod',
    namespace: 'default',
    queryParameters: [
        'command' => 'bash',
        'stdin' => true,
        'stdout' => true,
        'stderr' => true,
        'tty' => false,
    ]
);

// Use the helper to simplify execution
$helper = new ExecHelper($connection);

// Execute a simple command
$result = $helper->executeCommand("ls -lsa");
echo $result['stdout'];
echo "Exit code: " . ($result['exitCode'] ?? 'N/A') . "\n";

// Close the connection
$connection->close();
```

## Executing Multiple Commands

The helper allows you to easily execute multiple commands in the same pod:

```php
$results = $helper->executeCommands([
    "pwd",
    "whoami",
    "echo 'Hello from Kubernetes'",
    "date",
]);

foreach ($results as $cmdResult) {
    echo "\n$ " . $cmdResult['command'] . "\n";
    echo $cmdResult['stdout'];
    if ($cmdResult['exitCode'] !== 0) {
        echo "⚠ Exit code: " . $cmdResult['exitCode'] . "\n";
    }
}
```

## Error Handling

Errors are captured in the result via `stderr` and the exit code:

```php
$errorResult = $helper->executeCommand("ls /nonexistent");
echo "STDOUT: " . $errorResult['stdout'];
echo "STDERR: " . $errorResult['stderr'];
echo "Exit code: " . $errorResult['exitCode'] . "\n";
```

## How it Works

1. `connectPostPodExec()` establishes a WebSocket connection with the pod
2. The `stdin`, `stdout`, `stderr` parameters control the input/output streams
3. `ExecHelper` simplifies sending commands and reading results
4. Each result contains `stdout`, `stderr`, `exitCode` and `command`


## Notes

- The WebSocket connection must be closed after use with `$connection->close()`
