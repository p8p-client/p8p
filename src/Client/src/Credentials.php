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

class Credentials
{
    public function __construct(
        public readonly string $endpoint,
        public readonly ?string $token = null,
        public readonly ?string $caFile = null,
        public readonly ?string $certificateFile = null,
        public readonly ?string $privateKeyFile = null,
        public readonly ?string $httpUser = null,
        public readonly ?string $httpPassword = null,
    ) {
    }
}
