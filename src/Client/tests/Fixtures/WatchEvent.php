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

namespace P8p\Client\Tests\Fixtures;

class WatchEvent
{
    /**
     * @param array<mixed>|object $object
     */
    public function __construct(
        public array|object $object,
        public string $type,
    ) {
    }
}
