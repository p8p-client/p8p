<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Model;

readonly class ClassMetadata
{
    public function __construct(
        public string $name,
        public string $path,
    ) {
    }

    public function getShortName(): string
    {
        $parts = explode('\\', $this->name);

        return end($parts);
    }

    public function getNamespace(): string
    {
        $parts = explode('\\', $this->name);
        array_pop($parts);

        return implode('\\', $parts);
    }
}
