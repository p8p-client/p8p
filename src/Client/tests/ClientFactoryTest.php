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
use P8p\Client\ClientFactory;
use PHPUnit\Framework\TestCase;

class ClientFactoryTest extends TestCase
{
    public function testFromUrl(): void
    {
        $factory = ClientFactory::fromUrl('https://exmple.com:1234');
        $this->assertInstanceOf(ClientFactory::class, $factory);

        $client = $factory->getClient();
        $this->assertInstanceOf(Client::class, $client);
    }
}
