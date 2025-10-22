<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\WebSocket;

use Amp\Websocket\WebsocketClient;
use Amp\Websocket\WebsocketMessage;
use P8p\Client\Exception\WebSocketException;

/**
 * WebSocket connection wrapper for Kubernetes connect APIs.
 *
 * Handles bidirectional communication with Kubernetes APIs that require WebSocket connections,
 * such as exec, attach, and portforward.
 */
class WebSocketConnection
{
    public function __construct(
        private readonly WebsocketClient $connection,
    ) {
    }

    /**
     * Send a text message to the WebSocket connection.
     *
     * @param string $data The text data to send
     */
    public function sendText(string $data): void
    {
        try {
            $this->connection->sendText($data);
        } catch (\Throwable $e) {
            throw new WebSocketException('Failed to send text message: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Send binary data to the WebSocket connection.
     *
     * @param string $data The binary data to send
     */
    public function sendBinary(string $data): void
    {
        try {
            $this->connection->sendBinary($data);
        } catch (\Throwable $e) {
            throw new WebSocketException('Failed to send binary message: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Receive the next message from the WebSocket connection.
     *
     * @return WebsocketMessage|null Returns null if connection is closed
     */
    public function receive(): ?WebsocketMessage
    {
        try {
            return $this->connection->receive();
        } catch (\Throwable $e) {
            throw new WebSocketException('Failed to receive message: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Get an iterator for receiving messages.
     *
     * @return \Generator<WebsocketMessage>
     */
    public function getIterator(): \Generator
    {
        while ($message = $this->receive()) {
            yield $message;
        }
    }

    /**
     * Close the WebSocket connection.
     */
    public function close(): void
    {
        try {
            $this->connection->close();
        } catch (\Throwable $e) {
            throw new WebSocketException('Failed to close connection: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Check if the connection is closed.
     */
    public function isClosed(): bool
    {
        return $this->connection->isClosed();
    }

    /**
     * Get the underlying WebSocket client.
     *
     * This provides direct access to amphp's WebsocketClient for advanced use cases.
     */
    public function getUnderlyingConnection(): WebsocketClient
    {
        return $this->connection;
    }
}
