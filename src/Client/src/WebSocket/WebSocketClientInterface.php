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

interface WebSocketClientInterface
{
    /**
     * Create a WebSocket connection.
     *
     * @param string                    $path            The WebSocket endpoint path
     * @param array<string, string|int> $queryParameters Query parameters to append to the URL
     */
    public function connect(
        string $path,
        array $queryParameters = [],
    ): WebSocketConnection;
}
