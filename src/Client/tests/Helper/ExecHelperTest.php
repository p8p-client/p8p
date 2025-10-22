<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\tests\Helper;

use Amp\Websocket\WebsocketMessage;
use P8p\Client\Helper\ExecHelper;
use P8p\Client\WebSocket\WebSocketConnection;
use PHPUnit\Framework\TestCase;

class ExecHelperTest extends TestCase
{
    /** @var WebSocketConnection&\PHPUnit\Framework\MockObject\MockObject */
    private WebSocketConnection $mockConnection;
    private ExecHelper $helper;

    protected function setUp(): void
    {
        $this->mockConnection = $this->createMock(WebSocketConnection::class);
        $this->helper = new ExecHelper($this->mockConnection);
    }

    public function testStdin(): void
    {
        $this->mockConnection->expects($this->once())
            ->method('sendBinary')
            ->with("\x00ls -la\n");

        $this->helper->stdin("ls -la\n");
    }

    public function testReadNext(): void
    {
        $message = WebsocketMessage::fromText("\x01test output");

        $this->mockConnection->expects($this->once())
            ->method('receive')
            ->willReturn($message);

        $result = $this->helper->readNext();

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['channel']);
        $this->assertEquals('test output', $result['data']);
    }

    public function testReadNextReturnsNullWhenConnectionClosed(): void
    {
        $this->mockConnection->expects($this->once())
            ->method('receive')
            ->willReturn(null);

        $result = $this->helper->readNext();

        $this->assertNull($result);
    }

    public function testClose(): void
    {
        $this->mockConnection->expects($this->once())
            ->method('close');

        $this->helper->close();
    }

    public function testIsClosed(): void
    {
        $this->mockConnection->expects($this->once())
            ->method('isClosed')
            ->willReturn(true);

        $result = $this->helper->isClosed();

        $this->assertTrue($result);
    }

    public function testGetConnection(): void
    {
        $result = $this->helper->getConnection();

        $this->assertSame($this->mockConnection, $result);
    }

    public function testExecuteCommand(): void
    {
        $capturedMarker = null;

        // Capture the marker from stdin
        $this->mockConnection->expects($this->once())
            ->method('sendBinary')
            ->willReturnCallback(function ($data) use (&$capturedMarker) {
                // Extract marker from command like: echo "EXIT_CODE:$?:MARKER"
                if (preg_match('/EXIT_CODE:\$\?:(___P8P_EXEC_DONE_[^"]+)/', $data, $matches)) {
                    $capturedMarker = $matches[1];
                }
            });

        // Return the stdout and marker messages
        $this->mockConnection->expects($this->exactly(2))
            ->method('receive')
            ->willReturnCallback(function () use (&$capturedMarker) {
                static $call = 0;
                ++$call;
                if (1 === $call) {
                    return WebsocketMessage::fromText("\x01hello world\n");
                }

                return WebsocketMessage::fromText("\x01EXIT_CODE:0:{$capturedMarker}\n");
            });

        $result = $this->helper->executeCommand('echo "hello world"');

        $this->assertArrayHasKey('stdout', $result);
        $this->assertArrayHasKey('stderr', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('exitCode', $result);
        $this->assertStringContainsString('hello world', $result['stdout']);
        $this->assertSame('', $result['stderr']);
        $this->assertSame(0, $result['exitCode']);
    }

    public function testExecuteCommands(): void
    {
        $markers = [];

        // Capture markers from each command
        $this->mockConnection->expects($this->exactly(2))
            ->method('sendBinary')
            ->willReturnCallback(function ($data) use (&$markers) {
                if (preg_match('/EXIT_CODE:\$\?:(___P8P_EXEC_DONE_[^"]+)/', $data, $matches)) {
                    $markers[] = $matches[1];
                }
            });

        // Return output for each command
        $this->mockConnection->expects($this->exactly(4))
            ->method('receive')
            ->willReturnCallback(function () use (&$markers) {
                static $call = 0;
                ++$call;

                return match ($call) {
                    1 => WebsocketMessage::fromText("\x01output1\n"),
                    2 => WebsocketMessage::fromText("\x01EXIT_CODE:0:{$markers[0]}\n"),
                    3 => WebsocketMessage::fromText("\x01output2\n"),
                    4 => WebsocketMessage::fromText("\x01EXIT_CODE:0:{$markers[1]}\n"),
                    default => throw new \LogicException("Unexpected call number: {$call}"),
                };
            });

        $results = $this->helper->executeCommands(['cmd1', 'cmd2']);

        $this->assertCount(2, $results);

        $this->assertSame('cmd1', $results[0]['command']);
        $this->assertArrayHasKey('stdout', $results[0]);
        $this->assertArrayHasKey('exitCode', $results[0]);
        $this->assertEquals("output1\n", $results[0]['stdout']);
        $this->assertEquals(0, $results[0]['exitCode']);

        $this->assertSame('cmd2', $results[1]['command']);
        $this->assertArrayHasKey('stdout', $results[1]);
        $this->assertArrayHasKey('exitCode', $results[1]);
        $this->assertEquals("output2\n", $results[1]['stdout']);
        $this->assertEquals(0, $results[1]['exitCode']);
    }
}
