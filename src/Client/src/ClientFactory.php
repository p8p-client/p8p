<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client;

use P8p\Client\Credentials\CredentialsProviderInterface;
use P8p\Client\Credentials\InClusterProvider;
use P8p\Client\Credentials\KubeConfigProvider;
use P8p\Client\Credentials\UrlProvider;
use P8p\Client\Serializer\K8sSerializer;
use P8p\Client\WebSocket\WebSocketClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ClientFactory
{
    private readonly HttpClientInterface $httpClient;
    private readonly SerializerInterface $serializer;

    public static function fromUrl(
        string $endpoint,
        ?string $token = null,
        ?string $caFile = null,
        ?string $certificationFile = null,
        ?string $privateKeyFile = null,
        ?string $httpUser = null,
        ?string $httpPassword = null,
    ): self {
        return new self(new UrlProvider(
            endpoint: $endpoint,
            token: $token,
            caFile: $caFile,
            certificationFile: $certificationFile,
            privateKeyFile: $privateKeyFile,
            httpUser: $httpUser,
            httpPassword: $httpPassword
        ));
    }

    public static function fromInClusterConfiguration(): self
    {
        return new self(new InClusterProvider());
    }

    public static function fromKubeConfig(string $path, ?string $context = null): self
    {
        return new self(new KubeConfigProvider($path, $context));
    }

    public function __construct(
        private readonly CredentialsProviderInterface $credentialsProvider,
        ?HttpClientInterface $httpClient = null,
        ?SerializerInterface $serializer = null)
    {
        $this->httpClient = $httpClient ?? HttpClient::create();
        $this->serializer = $serializer ?? K8sSerializer::createDefault();
    }

    public function getClient(): Client
    {
        $credentials = $this->credentialsProvider->getCredentials();

        $options = [
            'base_uri' => $credentials->endpoint,
            'cafile' => $credentials->caFile,
            'local_cert' => $credentials->certificateFile,
            'local_pk' => $credentials->privateKeyFile,
            'auth_bearer' => $credentials->token,
        ];

        if ($credentials->httpUser) {
            $options['auth_basic'] = [$credentials->httpUser, $credentials->httpPassword];
        }

        $httpClient = $this->httpClient->withOptions($options);

        // Create WebSocket client for connect APIs
        $webSocketClient = new WebSocketClient($credentials);

        return new Client($httpClient, $webSocketClient, $this->serializer);
    }
}
