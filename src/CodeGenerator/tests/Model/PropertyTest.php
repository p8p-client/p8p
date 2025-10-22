<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Tests\Model;

use P8p\CodeGenerator\Model\Property;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

class PropertyTest extends TestCase
{
    public function testConstructorSetsAllProperties(): void
    {
        $type = Type::string();
        $property = new Property('username', 'The username field', $type);

        $this->assertSame('username', $property->name);
        $this->assertSame('The username field', $property->description);
        $this->assertSame($type, $property->type);
    }
}
