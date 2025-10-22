<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Tests;

use P8p\Client\Client;
use P8p\Client\Response;
use P8p\Client\WebSocket\WebSocketClientInterface;
use P8p\Client\WebSocket\WebSocketConnection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ClientTest extends TestCase
{
    private Client $client;
    private HttpClientInterface $httpClient;
    private MockResponse $response;
    /** @var WebSocketClientInterface&\PHPUnit\Framework\MockObject\MockObject */
    private WebSocketClientInterface $webSocketClient;

    protected function setUp(): void
    {
        $this->response = new MockResponse('body');
        $this->httpClient = new MockHttpClient($this->response);
        $this->webSocketClient = $this->createMock(WebSocketClientInterface::class);
        $this->client = new Client($this->httpClient, $this->webSocketClient, $this->createSerializer());
    }

    public function testMakeGetRequest(): void
    {
        $response = $this->client->makeRequest(
            verb: 'GET',
            path: '/test/{placeholder}',
            pathParameters: ['placeholder' => 'default'],
            queryParameters: ['p1' => 'v1']
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('https://example.com/test/default?p1=v1', $this->response->getRequestUrl());
        $this->assertEquals('GET', $this->response->getRequestMethod());

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('body', $response->getContent());
    }

    public function testMakePostRequest(): void
    {
        $response = $this->client->makeRequest(
            verb: 'POST',
            path: '/test/{placeholder}',
            pathParameters: ['placeholder' => 'default'],
            body: 'body'
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('https://example.com/test/default', $this->response->getRequestUrl());
        $this->assertEquals('POST', $this->response->getRequestMethod());
        $this->assertEquals('"body"', $this->response->getRequestOptions()['body']);
    }

    public function testMakeWebSocketConnection(): void
    {
        $mockConnection = $this->createMock(WebSocketConnection::class);

        $this->webSocketClient->expects($this->once())
            ->method('connect')
            ->with(
                '/api/v1/namespaces/default/pods/test-pod/exec',
                ['command' => 'bash'],
            )
            ->willReturn($mockConnection);

        $connection = $this->client->makeWebSocketConnection(
            path: '/api/v1/namespaces/default/pods/test-pod/exec',
            queryParameters: ['command' => 'bash'],
        );

        $this->assertInstanceOf(WebSocketConnection::class, $connection);
        $this->assertSame($mockConnection, $connection);
    }

    public function testMakeWebSocketConnectionWithPathParameters(): void
    {
        $mockConnection = $this->createMock(WebSocketConnection::class);

        $this->webSocketClient->expects($this->once())
            ->method('connect')
            ->with(
                '/api/v1/namespaces/default/pods/my-pod/portforward',
                ['ports' => '8080'],
            )
            ->willReturn($mockConnection);

        $connection = $this->client->makeWebSocketConnection(
            path: '/api/v1/namespaces/{namespace}/pods/{name}/portforward',
            pathParameters: ['namespace' => 'default', 'name' => 'my-pod'],
            queryParameters: ['ports' => '8080']
        );

        $this->assertInstanceOf(WebSocketConnection::class, $connection);
    }

    public function testMakeWebSocketConnectionWithoutParameters(): void
    {
        $mockConnection = $this->createMock(WebSocketConnection::class);

        $this->webSocketClient->expects($this->once())
            ->method('connect')
            ->with('/api/v1/watch/pods', [])
            ->willReturn($mockConnection);

        $connection = $this->client->makeWebSocketConnection(
            path: '/api/v1/watch/pods'
        );

        $this->assertInstanceOf(WebSocketConnection::class, $connection);
    }

    private function createSerializer(): Serializer
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        return new Serializer($normalizers, $encoders);
    }
}
