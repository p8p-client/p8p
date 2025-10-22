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

use P8p\Client\Credentials;
use PHPUnit\Framework\TestCase;

class UrlProviderTest extends TestCase
{
    public function testProvider(): void
    {
        $provider = new Credentials\UrlProvider(
            endpoint: 'https://my-cluster:6443',
            token: '1234',
            caFile: '/tmp/ca-cert.pem',
            certificationFile: '/tmp/client-cert.pem',
            privateKeyFile: '/tmp/client-key.pem',
            httpUser: 'user',
            httpPassword: 'password',
        );

        $credentials = $provider->getCredentials();

        $this->assertEquals('https://my-cluster:6443', $credentials->endpoint);
        $this->assertEquals('1234', $credentials->token);
        $this->assertEquals('/tmp/ca-cert.pem', $credentials->caFile);
        $this->assertEquals('/tmp/client-cert.pem', $credentials->certificateFile);
        $this->assertEquals('/tmp/client-key.pem', $credentials->privateKeyFile);
        $this->assertEquals('user', $credentials->httpUser);
        $this->assertEquals('password', $credentials->httpPassword);
    }
}
