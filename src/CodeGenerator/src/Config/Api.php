<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Config;

readonly class Api
{
    public function __construct(
        public string $group,
        public string $version,
    ) {
    }

    public function isCore(): bool
    {
        return '' === $this->group;
    }
}
