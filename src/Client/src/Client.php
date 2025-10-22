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

use League\Uri\UriTemplate;
use P8p\Client\Api\ApiInterface;
use P8p\Client\WebSocket\WebSocketClientInterface;
use P8p\Client\WebSocket\WebSocketConnection;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
    /**
     * @var array<class-string, ApiInterface>
     */
    private array $apis = [];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly WebSocketClientInterface $webSocketClient,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @template T of ApiInterface
     *
     * @param class-string<T> $apiClass
     *
     * @return T
     */
    public function getApi(string $apiClass): ApiInterface
    {
        if (!isset($this->apis[$apiClass])) {
            $this->apis[$apiClass] = new $apiClass();
            $this->apis[$apiClass]->setClient($this);
        }

        return $this->apis[$apiClass]; /* @phpstan-ignore return.type */
    }

    /**
     * @template T
     *
     * @param ?class-string<T>      $responseClass
     * @param array<string, string> $pathParameters
     * @param array<string, string> $queryParameters
     *
     * @return Response<T>
     */
    public function makeRequest(
        string $verb,
        string $path,
        array $pathParameters = [],
        ?string $responseClass = null,
        mixed $body = null,
        array $queryParameters = []): Response
    {
        $options = [
            'headers' => [
                'Content-Type' => 'PATCH' === $verb ? 'application/merge-patch+json' : 'application/json',
                'Accept' => 'application/json',
            ],
            'query' => $queryParameters,
        ];

        if ($body) {
            $options['body'] = $this->serializer->serialize($body, 'json');
        }

        $uri = new UriTemplate($path)->expand($pathParameters)->toString();

        $httpResponse = $this->httpClient->request($verb, $uri, $options);

        /** @var Response<T> */
        $response = new Response($httpResponse, $this->httpClient, $this->serializer, $responseClass);

        return $response;
    }

    /**
     * @param string                    $path            The WebSocket endpoint path
     * @param array<string, string>     $pathParameters  Path parameters to expand in the URI template
     * @param array<string, string|int> $queryParameters Query parameters to append to the URL
     *
     * @throws Exception\WebSocketException
     */
    public function makeWebSocketConnection(
        string $path,
        array $pathParameters = [],
        array $queryParameters = [],
    ): WebSocketConnection {
        $expandedPath = new UriTemplate($path)->expand($pathParameters)->toString();

        return $this->webSocketClient->connect($expandedPath, $queryParameters);
    }
}
