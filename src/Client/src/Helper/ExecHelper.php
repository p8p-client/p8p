<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Helper;

use P8p\Client\WebSocket\WebSocketConnection;

/**
 * Helper class for Kubernetes exec WebSocket connections.
 *
 * Simplifies interaction with Kubernetes exec API by handling the stream multiplexing protocol.
 * In the Kubernetes exec protocol, each message is prefixed with a byte indicating the stream:
 * - 0: stdin
 * - 1: stdout
 * - 2: stderr
 * - 3: error stream
 *
 * Usage:
 *   $helper = new ExecHelper($connection);
 *   $result = $helper->executeCommand("ls -la");
 *   echo $result['stdout'];
 */
class ExecHelper
{
    private const int CHANNEL_STDIN = 0;
    private const int CHANNEL_STDOUT = 1;
    private const int CHANNEL_STDERR = 2;
    private const int CHANNEL_ERROR = 3;

    public function __construct(
        private readonly WebSocketConnection $connection,
    ) {
    }

    /**
     * Send data to stdin (channel 0).
     *
     * Useful for interactive commands or when you need manual control.
     *
     * @param string $data The data to send to stdin
     */
    public function stdin(string $data): void
    {
        $this->connection->sendBinary(\chr(self::CHANNEL_STDIN).$data);
    }

    /**
     * Close the underlying WebSocket connection.
     */
    public function close(): void
    {
        $this->connection->close();
    }

    /**
     * Check if the underlying connection is closed.
     */
    public function isClosed(): bool
    {
        return $this->connection->isClosed();
    }

    /**
     * Get the underlying WebSocket connection for advanced usage.
     */
    public function getConnection(): WebSocketConnection
    {
        return $this->connection;
    }

    /**
     * Read the next message from any channel.
     *
     * Low-level method for advanced usage. Most users should use executeCommand() instead.
     *
     * @return array{channel: int, data: string}|null Message with channel and data, or null if connection is closed
     */
    public function readNext(): ?array
    {
        $message = $this->connection->receive();

        if (null === $message) {
            return null;
        }

        $content = $message->buffer();

        if ('' === $content) {
            return null;
        }

        $channel = \ord($content[0]);
        $data = substr($content, 1);

        return [
            'channel' => $channel,
            'data' => $data,
        ];
    }

    /**
     * Execute a command and automatically collect all output.
     *
     * This method automatically appends a unique marker after the command to detect when
     * execution is complete. It works by running: your_command && echo "MARKER" || echo "MARKER"
     *
     * @param string $command     The command to execute (without trailing newline)
     * @param int    $maxMessages Maximum number of messages to read (default: 1000, safety limit)
     *
     * @return array{stdout: string, stderr: string, error: string, exitCode: int|null}
     */
    public function executeCommand(string $command, int $maxMessages = 1000): array
    {
        $marker = '___P8P_EXEC_DONE_'.uniqid().'___';

        // Wrap command to add marker and capture exit code
        $wrappedCommand = sprintf(
            "%s; echo \"EXIT_CODE:\$?:%s\"\n",
            rtrim($command),
            $marker
        );

        $result = [
            'stdout' => '',
            'stderr' => '',
            'error' => '',
            'exitCode' => null,
        ];

        // Send the command
        $this->stdin($wrappedCommand);

        $messageCount = 0;

        // Read messages until we see the marker
        while ($messageCount < $maxMessages) {
            $message = $this->readNext();

            if (null === $message) {
                break;
            }

            ++$messageCount;

            // Collect output by channel
            switch ($message['channel']) {
                case self::CHANNEL_STDOUT:
                    $result['stdout'] .= $message['data'];
                    break;
                case self::CHANNEL_STDERR:
                    $result['stderr'] .= $message['data'];
                    break;
                case self::CHANNEL_ERROR:
                    $result['error'] .= $message['data'];
                    break;
            }

            // Check if we've received the completion marker
            if (str_contains($result['stdout'], $marker)) {
                // Extract exit code if present
                if (preg_match('/EXIT_CODE:(\d+):'.$marker.'/', $result['stdout'], $matches)) {
                    $result['exitCode'] = (int) $matches[1];
                }

                // Remove the marker and exit code from output
                $result['stdout'] = preg_replace('/EXIT_CODE:\d+:'.$marker.'\s*/', '', $result['stdout']) ?? $result['stdout'];
                break;
            }
        }

        return $result;
    }

    /**
     * Execute multiple commands sequentially.
     *
     * Each command is executed with automatic completion detection.
     *
     * @param array<string> $commands Array of commands to execute
     *
     * @return array<array{command: string, stdout: string, stderr: string, error: string, exitCode: int|null}>
     */
    public function executeCommands(array $commands): array
    {
        $results = [];

        foreach ($commands as $command) {
            $output = $this->executeCommand($command);
            $results[] = array_merge(['command' => $command], $output);
        }

        return $results;
    }
}
