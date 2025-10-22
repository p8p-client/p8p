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

use Amp\Http\Client\Connection\DefaultConnectionFactory;
use Amp\Http\Client\Connection\UnlimitedConnectionPool;
use Amp\Http\Client\HttpClient;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Socket\Certificate;
use Amp\Socket\ClientTlsContext;
use Amp\Socket\ConnectContext;
use Amp\Websocket\Client\Rfc6455Connector;
use Amp\Websocket\Client\WebsocketHandshake;
use P8p\Client\Credentials;
use P8p\Client\Exception\WebSocketException;

class WebSocketClient implements WebSocketClientInterface
{
    public function __construct(
        private readonly Credentials $credentials,
    ) {
    }

    public function connect(
        string $path,
        array $queryParameters = [],
    ): WebSocketConnection {
        try {
            $wsUrl = $this->convertToWebSocketUrl($this->credentials->endpoint);

            $fullUrl = rtrim($wsUrl, '/').'/'.ltrim($path, '/');
            if (!empty($queryParameters)) {
                $fullUrl .= '?'.http_build_query($queryParameters);
            }

            $handshake = new WebsocketHandshake($fullUrl);

            // Add authentication headers
            if ($this->credentials->token) {
                $handshake = $handshake->withHeader('Authorization', 'Bearer '.$this->credentials->token);
            }

            if ($this->credentials->httpUser && $this->credentials->httpPassword) {
                $credentials = base64_encode($this->credentials->httpUser.':'.$this->credentials->httpPassword);
                $handshake = $handshake->withHeader('Authorization', 'Basic '.$credentials);
            }

            $connectContext = $this->createConnectContext();
            $httpClient = $this->createHttpClient($connectContext);
            $connector = new Rfc6455Connector(httpClient: $httpClient);

            $connection = $connector->connect($handshake);

            return new WebSocketConnection($connection);
        } catch (\Throwable $e) {
            throw new WebSocketException('Failed to create WebSocket connection: '.$e->getMessage(), 0, $e);
        }
    }

    private function convertToWebSocketUrl(string $httpUrl): string
    {
        return preg_replace('/^http(s)?:\/\//', 'ws$1://', $httpUrl) ?? $httpUrl;
    }

    /**
     * Create a connection context with TLS configuration.
     */
    private function createConnectContext(): ConnectContext
    {
        $connectContext = new ConnectContext();

        // Configure TLS if needed
        if (str_starts_with($this->credentials->endpoint, 'https://')) {
            $tlsContext = new ClientTlsContext('');

            if ($this->credentials->caFile && file_exists($this->credentials->caFile)) {
                $tlsContext = $tlsContext->withCaFile($this->credentials->caFile);
            }

            if ($this->credentials->certificateFile && $this->credentials->privateKeyFile) {
                if (file_exists($this->credentials->certificateFile) && file_exists($this->credentials->privateKeyFile)) {
                    $certificate = new Certificate(
                        $this->credentials->certificateFile,
                        $this->credentials->privateKeyFile
                    );
                    $tlsContext = $tlsContext->withCertificate($certificate);
                }
            }

            $connectContext = $connectContext->withTlsContext($tlsContext);
        }

        return $connectContext;
    }

    private function createHttpClient(ConnectContext $connectContext): HttpClient
    {
        $connectionFactory = new DefaultConnectionFactory(
            connectContext: $connectContext
        );

        $pool = new UnlimitedConnectionPool($connectionFactory);

        return new HttpClientBuilder()
            ->usingPool($pool)
            ->build();
    }
}
