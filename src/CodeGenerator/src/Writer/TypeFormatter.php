<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Writer;

use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\GenericType;
use Symfony\Component\TypeInfo\Type\IntersectionType;
use Symfony\Component\TypeInfo\Type\NullableType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\Type\UnionType;

/**
 * Formats Symfony TypeInfo types for PHP code generation.
 */
class TypeFormatter
{
    /**
     * Convert Type to valid PHP type declaration (for parameters, properties, return types).
     */
    public static function toPhpType(Type $type): string
    {
        return match (true) {
            $type instanceof GenericType => 'array',
            $type instanceof CollectionType => 'array',
            $type instanceof ObjectType => $type->getClassName(),
            $type instanceof UnionType => self::formatUnionPhp($type),
            $type instanceof IntersectionType => self::formatIntersectionPhp($type),
            default => (string) $type,
        };
    }

    /**
     * Convert Type to PHPDoc type annotation (with generics and short class names).
     */
    public static function toPhpDocType(Type $type, bool $useShortNames = true): string
    {
        return match (true) {
            $type instanceof GenericType => self::formatGenericPhpDoc($type, $useShortNames),
            $type instanceof CollectionType => self::formatCollectionPhpDoc($type, $useShortNames),
            $type instanceof ObjectType => $useShortNames ? self::getShortClassName($type->getClassName()) : '\\'.$type->getClassName(),
            $type instanceof UnionType => self::formatUnionPhpDoc($type, $useShortNames),
            $type instanceof IntersectionType => self::formatIntersectionPhpDoc($type, $useShortNames),
            default => (string) $type,
        };
    }

    /**
     * Get all fully qualified class names used in a type (for use statements).
     *
     * @return array<string>
     */
    public static function extractAllClassNames(Type $type): array
    {
        $classNames = [];

        if ($type instanceof ObjectType) {
            $classNames[] = $type->getClassName();
        }

        if ($type instanceof GenericType) {
            // Get class name from the wrapped type
            $wrappedType = $type->getWrappedType();
            if ($wrappedType instanceof ObjectType) {
                $classNames[] = $wrappedType->getClassName();
            }

            // Get class names from variable types
            foreach ($type->getVariableTypes() as $varType) {
                $classNames = [...$classNames, ...self::extractAllClassNames($varType)];
            }
        }

        if ($type instanceof CollectionType) {
            $classNames = [...$classNames, ...self::extractAllClassNames($type->getCollectionValueType())];
        }

        if ($type instanceof UnionType || $type instanceof IntersectionType) {
            foreach ($type->getTypes() as $subType) {
                $classNames = [...$classNames, ...self::extractAllClassNames($subType)];
            }
        }

        if ($type instanceof NullableType) {
            $classNames = [...$classNames, ...self::extractAllClassNames($type->getWrappedType())];
        }

        return array_unique($classNames);
    }

    /**
     * Get short class name (without namespace).
     */
    private static function getShortClassName(string $fqcn): string
    {
        $parts = explode('\\', $fqcn);

        return end($parts);
    }

    private static function formatGenericPhpDoc(GenericType $type, bool $useShortNames): string
    {
        $wrappedType = $type->getWrappedType();
        $wrappedStr = $wrappedType instanceof ObjectType && $useShortNames
            ? self::getShortClassName($wrappedType->getClassName())
            : (string) $wrappedType;

        $variableTypesStr = implode(', ', array_map(
            fn (Type $t) => self::toPhpDocType($t, $useShortNames),
            $type->getVariableTypes()
        ));

        return "{$wrappedStr}<{$variableTypesStr}>";
    }

    private static function formatCollectionPhpDoc(CollectionType $type, bool $useShortNames): string
    {
        $wrappedType = $type->getWrappedType();

        // If it's a generic collection, format with generics
        if ($wrappedType instanceof GenericType) {
            return self::formatGenericPhpDoc($wrappedType, $useShortNames);
        }

        // If it's a list
        if ($type->isList()) {
            $valueType = self::toPhpDocType($type->getCollectionValueType(), $useShortNames);

            return "list<{$valueType}>";
        }

        // Otherwise get the value type
        $valueType = $type->getCollectionValueType();
        $valueTypeStr = self::toPhpDocType($valueType, $useShortNames);

        // If it's array with typed values
        if ($wrappedType->isIdentifiedBy('array')) {
            return "array<{$valueTypeStr}>";
        }

        // Otherwise return as-is
        return (string) $wrappedType;
    }

    private static function formatUnionPhp(UnionType $type): string
    {
        $types = array_map(fn (Type $t) => self::toPhpType($t), $type->getTypes());

        return implode('|', $types);
    }

    private static function formatUnionPhpDoc(UnionType $type, bool $useShortNames): string
    {
        $types = array_map(fn (Type $t) => self::toPhpDocType($t, $useShortNames), $type->getTypes());

        return implode('|', $types);
    }

    private static function formatIntersectionPhp(IntersectionType $type): string
    {
        $types = array_map(fn (Type $t) => self::toPhpType($t), $type->getTypes());

        return implode('&', $types);
    }

    private static function formatIntersectionPhpDoc(IntersectionType $type, bool $useShortNames): string
    {
        $types = array_map(fn (Type $t) => self::toPhpDocType($t, $useShortNames), $type->getTypes());

        return implode('&', $types);
    }
}
