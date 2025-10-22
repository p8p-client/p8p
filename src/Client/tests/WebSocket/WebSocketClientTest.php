<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Tests\WebSocket;

use P8p\Client\Credentials;
use P8p\Client\Exception\WebSocketException;
use P8p\Client\WebSocket\WebSocketClient;
use PHPUnit\Framework\TestCase;

class WebSocketClientTest extends TestCase
{
    public function testConstructorWithTokenCredentials(): void
    {
        $credentials = new Credentials(
            endpoint: 'https://kubernetes.example.com',
            token: 'test-token'
        );

        $factory = new WebSocketClient($credentials);

        $this->assertInstanceOf(WebSocketClient::class, $factory);
    }

    public function testConstructorWithBasicAuthCredentials(): void
    {
        $credentials = new Credentials(
            endpoint: 'https://kubernetes.example.com',
            httpUser: 'admin',
            httpPassword: 'secret'
        );

        $factory = new WebSocketClient($credentials);

        $this->assertInstanceOf(WebSocketClient::class, $factory);
    }

    public function testConstructorWithCertificateCredentials(): void
    {
        $credentials = new Credentials(
            endpoint: 'https://kubernetes.example.com',
            certificateFile: '/path/to/cert.pem',
            privateKeyFile: '/path/to/key.pem',
            caFile: '/path/to/ca.pem'
        );

        $factory = new WebSocketClient($credentials);

        $this->assertInstanceOf(WebSocketClient::class, $factory);
    }

    public function testConnectThrowsExceptionOnInvalidUrl(): void
    {
        $credentials = new Credentials(endpoint: 'invalid://url');
        $factory = new WebSocketClient($credentials);

        $this->expectException(WebSocketException::class);
        $this->expectExceptionMessage('Failed to create WebSocket connection');

        $factory->connect('/api/v1/pods');
    }

    public function testConnectThrowsExceptionOnUnreachableServer(): void
    {
        $credentials = new Credentials(endpoint: 'http://localhost:99999');
        $factory = new WebSocketClient($credentials);

        $this->expectException(WebSocketException::class);
        $this->expectExceptionMessage('Failed to create WebSocket connection');

        $factory->connect('/api/v1/pods');
    }
}
