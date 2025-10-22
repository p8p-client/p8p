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

use Amp\Websocket\WebsocketClient;
use Amp\Websocket\WebsocketMessage;
use P8p\Client\Exception\WebSocketException;
use P8p\Client\WebSocket\WebSocketConnection;
use PHPUnit\Framework\TestCase;

class WebSocketConnectionTest extends TestCase
{
    /** @var WebsocketClient&\PHPUnit\Framework\MockObject\MockObject */
    private WebsocketClient $mockClient;
    private WebSocketConnection $connection;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(WebsocketClient::class);
        $this->connection = new WebSocketConnection($this->mockClient);
    }

    public function testSendText(): void
    {
        $this->mockClient->expects($this->once())
            ->method('sendText')
            ->with('test message');

        $this->connection->sendText('test message');
    }

    public function testSendTextThrowsExceptionOnFailure(): void
    {
        $this->mockClient->expects($this->once())
            ->method('sendText')
            ->willThrowException(new \RuntimeException('Connection failed'));

        $this->expectException(WebSocketException::class);
        $this->expectExceptionMessage('Failed to send text message');

        $this->connection->sendText('test message');
    }

    public function testSendBinary(): void
    {
        $binaryData = "\x00\x01\x02\x03";

        $this->mockClient->expects($this->once())
            ->method('sendBinary')
            ->with($binaryData);

        $this->connection->sendBinary($binaryData);
    }

    public function testSendBinaryThrowsExceptionOnFailure(): void
    {
        $this->mockClient->expects($this->once())
            ->method('sendBinary')
            ->willThrowException(new \RuntimeException('Connection failed'));

        $this->expectException(WebSocketException::class);
        $this->expectExceptionMessage('Failed to send binary message');

        $this->connection->sendBinary('data');
    }

    public function testReceive(): void
    {
        $this->mockClient->expects($this->once())
            ->method('receive')
            ->willReturn(WebsocketMessage::fromText('test'));

        $result = $this->connection->receive();

        $this->assertNotNull($result);
        $this->assertEquals($result->buffer(), 'test');
    }

    public function testReceiveThrowsExceptionOnFailure(): void
    {
        $this->mockClient->expects($this->once())
            ->method('receive')
            ->willThrowException(new \RuntimeException('Connection error'));

        $this->expectException(WebSocketException::class);
        $this->expectExceptionMessage('Failed to receive message');

        $this->connection->receive();
    }

    public function testClose(): void
    {
        $this->mockClient->expects($this->once())
            ->method('close');

        $this->connection->close();
    }

    public function testCloseThrowsExceptionOnFailure(): void
    {
        $this->mockClient->expects($this->once())
            ->method('close')
            ->willThrowException(new \RuntimeException('Close failed'));

        $this->expectException(WebSocketException::class);
        $this->expectExceptionMessage('Failed to close connection');

        $this->connection->close();
    }

    public function testIsClosed(): void
    {
        $this->mockClient->expects($this->once())
            ->method('isClosed')
            ->willReturn(true);

        $result = $this->connection->isClosed();

        $this->assertTrue($result);
    }

    public function testGetUnderlyingConnection(): void
    {
        $result = $this->connection->getUnderlyingConnection();

        $this->assertSame($this->mockClient, $result);
    }
}
