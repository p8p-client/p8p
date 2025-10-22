<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Tests\Credentials;

use P8p\Client\Credentials\InClusterProvider;
use PHPUnit\Framework\TestCase;

class InClusterProviderTest extends TestCase
{
    public function testProvider(): void
    {
        $mock = $this->getMockBuilder(InClusterProvider::class)
            ->onlyMethods(['getFileContent'])
            ->getMock();

        $mock->expects($this->once())
            ->method('getFileContent')
            ->with($this->identicalTo('/var/run/secrets/kubernetes.io/serviceaccount/token'))
            ->willReturn('1234');

        $credentials = $mock->getCredentials();

        $this->assertEquals('https://kubernetes.default.svc', $credentials->endpoint);
        $this->assertEquals('1234', $credentials->token);
        $this->assertEquals('/var/run/secrets/kubernetes.io/serviceaccount/ca.crt', $credentials->caFile);
    }
}
