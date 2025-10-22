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

use Symfony\Component\TypeInfo\Type;

readonly class Property
{
    public function __construct(
        public string $name,
        public ?string $description,
        public Type $type,
    ) {
    }
}
