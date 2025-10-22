<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Tests\Fixtures\Api;

use P8p\Client\Api\AbstractApi;
use P8p\Client\Client;

class ConcreteTestApi extends AbstractApi
{
    public function getClient(): Client
    {
        return $this->client;
    }
}
