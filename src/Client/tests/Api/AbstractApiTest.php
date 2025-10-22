<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace P8p\Client\Tests\Api;

use P8p\Client\Client;
use P8p\Client\Tests\Fixtures\Api\ConcreteTestApi;
use PHPUnit\Framework\TestCase;

class AbstractApiTest extends TestCase
{
    /** @var Client&\PHPUnit\Framework\MockObject\MockObject */
    private Client $client;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
    }

    public function testSetClientStoresClientInstance(): void
    {
        $api = new ConcreteTestApi();

        $api->setClient($this->client);

        $this->assertSame($this->client, $api->getClient());
    }
}
