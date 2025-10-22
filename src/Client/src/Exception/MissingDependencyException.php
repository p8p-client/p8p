<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Exception;

/**
 * Exception thrown when an optional dependency is missing.
 */
class MissingDependencyException extends \RuntimeException
{
    public static function forPackage(string $package, string $feature, string $installCommand = ''): self
    {
        $message = sprintf(
            'The package "%s" is required to use %s. Please install it using: %s',
            $package,
            $feature,
            $installCommand ?: "composer require {$package}"
        );

        return new self($message);
    }
}
