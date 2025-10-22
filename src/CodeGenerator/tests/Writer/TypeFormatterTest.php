<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Tests\Writer;

use P8p\CodeGenerator\Writer\TypeFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

class TypeFormatterTest extends TestCase
{
    // Tests for toPhpType()

    public function testToPhpTypeWithPrimitiveTypes(): void
    {
        $this->assertSame('string', TypeFormatter::toPhpType(Type::string()));
        $this->assertSame('int', TypeFormatter::toPhpType(Type::int()));
        $this->assertSame('bool', TypeFormatter::toPhpType(Type::bool()));
        $this->assertSame('float', TypeFormatter::toPhpType(Type::float()));
        $this->assertSame('array', TypeFormatter::toPhpType(Type::array()));
    }

    public function testToPhpTypeWithObjectType(): void
    {
        $type = Type::object(\DateTime::class);
        $this->assertSame('DateTime', TypeFormatter::toPhpType($type));
    }

    public function testToPhpTypeWithObjectTypeFullyQualified(): void
    {
        $type = Type::object('App\\Model\\User');
        $this->assertSame('App\\Model\\User', TypeFormatter::toPhpType($type));
    }

    public function testToPhpTypeWithListType(): void
    {
        $type = Type::list(Type::int());
        $this->assertSame('array', TypeFormatter::toPhpType($type));
    }

    public function testToPhpTypeWithNullableType(): void
    {
        $type = Type::nullable(Type::string());
        $this->assertSame('null|string', TypeFormatter::toPhpType($type));
    }

    public function testToPhpTypeWithNullableObjectType(): void
    {
        $type = Type::nullable(Type::object('User'));
        $this->assertSame('User|null', TypeFormatter::toPhpType($type));
    }

    public function testToPhpTypeWithUnionType(): void
    {
        $type = Type::union(Type::string(), Type::int());
        $this->assertSame('int|string', TypeFormatter::toPhpType($type));
    }

    public function testToPhpTypeWithUnionOfObjects(): void
    {
        $type = Type::union(Type::object('User'), Type::object('Admin'));
        $this->assertSame('Admin|User', TypeFormatter::toPhpType($type));
    }

    public function testToPhpTypeWithIntersectionType(): void
    {
        $type = Type::intersection(Type::object(\Countable::class), Type::object(\Iterator::class));
        $this->assertSame('Countable&Iterator', TypeFormatter::toPhpType($type));
    }

    // Tests for toPhpDocType()

    public function testToPhpDocTypeWithPrimitiveTypes(): void
    {
        $this->assertSame('string', TypeFormatter::toPhpDocType(Type::string()));
        $this->assertSame('int', TypeFormatter::toPhpDocType(Type::int()));
        $this->assertSame('bool', TypeFormatter::toPhpDocType(Type::bool()));
    }

    public function testToPhpDocTypeWithObjectTypeShortName(): void
    {
        $type = Type::object('App\\Model\\User');
        $this->assertSame('User', TypeFormatter::toPhpDocType($type, useShortNames: true));
    }

    public function testToPhpDocTypeWithObjectTypeFullName(): void
    {
        $type = Type::object('App\\Model\\User');
        $this->assertSame('\\App\\Model\\User', TypeFormatter::toPhpDocType($type, useShortNames: false));
    }

    public function testToPhpDocTypeWithListTypeShortName(): void
    {
        $type = Type::list(Type::object('App\\Model\\User'));
        $this->assertSame('array<int, User>', TypeFormatter::toPhpDocType($type, useShortNames: true));
    }

    public function testToPhpDocTypeWithListTypeFullName(): void
    {
        $type = Type::list(Type::object('App\\Model\\User'));
        $this->assertSame('array<int, \\App\\Model\\User>', TypeFormatter::toPhpDocType($type, useShortNames: false));
    }

    public function testToPhpDocTypeWithListOfPrimitives(): void
    {
        $type = Type::list(Type::string());
        $this->assertSame('array<int, string>', TypeFormatter::toPhpDocType($type));
    }

    public function testToPhpDocTypeWithUnionTypeShortNames(): void
    {
        $type = Type::union(Type::object('App\\Model\\User'), Type::object('App\\Model\\Admin'));
        $this->assertSame('Admin|User', TypeFormatter::toPhpDocType($type, useShortNames: true));
    }

    public function testToPhpDocTypeWithUnionTypeFullNames(): void
    {
        $type = Type::union(Type::object('App\\Model\\User'), Type::object('App\\Model\\Admin'));
        $this->assertSame('\App\\Model\\Admin|\App\\Model\\User', TypeFormatter::toPhpDocType($type, useShortNames: false));
    }

    public function testToPhpDocTypeWithNullableObjectShortName(): void
    {
        $type = Type::nullable(Type::object('App\\Model\\User'));
        $this->assertSame('User|null', TypeFormatter::toPhpDocType($type, useShortNames: true));
    }

    public function testToPhpDocTypeWithIntersectionTypeShortNames(): void
    {
        $type = Type::intersection(Type::object('App\\Contracts\\Countable'), Type::object('App\\Contracts\\Iterator'));
        $this->assertSame('Countable&Iterator', TypeFormatter::toPhpDocType($type, useShortNames: true));
    }

    // Tests for extractAllClassNames()

    public function testExtractAllClassNamesFromPrimitiveType(): void
    {
        $type = Type::string();
        $this->assertEmpty(TypeFormatter::extractAllClassNames($type));
    }

    public function testExtractAllClassNamesFromObjectType(): void
    {
        $type = Type::object('App\\Model\\User');
        $this->assertSame(['App\\Model\\User'], TypeFormatter::extractAllClassNames($type));
    }

    public function testExtractAllClassNamesFromListOfObjects(): void
    {
        $type = Type::list(Type::object('App\\Model\\User'));
        $this->assertSame(['App\\Model\\User'], TypeFormatter::extractAllClassNames($type));
    }

    public function testExtractAllClassNamesFromUnionType(): void
    {
        $type = Type::union(
            Type::object('App\\Model\\User'),
            Type::object('App\\Model\\Admin'),
            Type::string()
        );
        $classNames = TypeFormatter::extractAllClassNames($type);
        $this->assertEqualsCanonicalizing(['App\\Model\\User', 'App\\Model\\Admin'], $classNames);
    }

    public function testExtractAllClassNamesFromNullableObjectType(): void
    {
        $type = Type::nullable(Type::object('App\\Model\\User'));
        $classNames = TypeFormatter::extractAllClassNames($type);
        $this->assertSame(['App\\Model\\User'], $classNames);
    }

    public function testExtractAllClassNamesFromIntersectionType(): void
    {
        $type = Type::intersection(
            Type::object(\Countable::class),
            Type::object(\Iterator::class)
        );
        $classNames = TypeFormatter::extractAllClassNames($type);
        $this->assertEqualsCanonicalizing(['Countable', 'Iterator'], $classNames);
    }

    public function testExtractAllClassNamesDeduplicates(): void
    {
        $type = Type::union(
            Type::object('User'),
            Type::object('User'),
            Type::object('Admin')
        );
        $classNames = TypeFormatter::extractAllClassNames($type);
        $this->assertCount(2, $classNames);
        $this->assertContains('User', $classNames);
        $this->assertContains('Admin', $classNames);
    }

    public function testExtractAllClassNamesFromComplexNestedType(): void
    {
        $type = Type::nullable(
            Type::union(
                Type::list(Type::object('App\\Model\\User')),
                Type::object('App\\Model\\Admin')
            )
        );
        $classNames = TypeFormatter::extractAllClassNames($type);
        $this->assertEqualsCanonicalizing(['App\\Model\\User', 'App\\Model\\Admin'], $classNames);
    }
}
