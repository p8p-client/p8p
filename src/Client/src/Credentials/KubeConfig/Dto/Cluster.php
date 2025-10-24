<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Credentials\KubeConfig\Dto;

readonly class Cluster
{
    public function __construct(
        public string $server,
        public string $name,
        public ?string $certificateAuthorityData = null,
    ) {
    }
}
