<?php

namespace P8p;

use P8p\Client\ClientFactory;
use P8p\Client\Helper\ExecHelper;
use P8p\Sdk\Api\Core\V1\PodExecOptionsApi;

include __DIR__.'/../src/Sdk/vendor/autoload.php';

$client = ClientFactory::fromUrl('http://127.0.0.1:8001')->getClient();;
$podExecApi = $client->getApi(PodExecOptionsApi::class);


// Execute a command in a pod using WebSocket
$connection = $podExecApi->connectPostPodExec(
    name: 'p8p-create-test',
    namespace: 'default',
    queryParameters: [
        'command' => urldecode('bash'),
        'stdin' => true,
        'stdout' => true,
        'stderr' => true,
        'tty' => false,
    ]
);

echo "WebSocket connection established\n\n";

$helper = new ExecHelper($connection);


// Simple usage - just execute a command and get the output!
echo "=== Single Command ===\n";
$result = $helper->executeCommand("ls -lsa");
echo $result['stdout'];
echo "Exit code: " . ($result['exitCode'] ?? 'N/A') . "\n";

// Execute multiple commands - super simple!
echo "\n=== Multiple Commands ===\n";
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

// Test error handling
echo "\n=== Error Handling ===\n";
$errorResult = $helper->executeCommand("ls /nonexistent");
echo "STDOUT: " . $errorResult['stdout'];
echo "STDERR: " . $errorResult['stderr'];
echo "Exit code: " . $errorResult['exitCode'] . "\n";


$connection->close();
echo "\n✓ Connection closed\n";
