<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Tests\Exception;

use P8p\Client\Exception\MissingDependencyException;
use PHPUnit\Framework\TestCase;

class MissingDependencyExceptionTest extends TestCase
{
    public function testForPackageCreatesExceptionWithDefaultInstallCommand(): void
    {
        $exception = MissingDependencyException::forPackage(
            package: 'vendor/package',
            feature: 'SomeFeature'
        );

        $this->assertInstanceOf(MissingDependencyException::class, $exception);
        $this->assertStringContainsString('vendor/package', $exception->getMessage());
        $this->assertStringContainsString('SomeFeature', $exception->getMessage());
        $this->assertStringContainsString('composer require vendor/package', $exception->getMessage());
    }

    public function testForPackageCreatesExceptionWithCustomInstallCommand(): void
    {
        $exception = MissingDependencyException::forPackage(
            package: 'vendor/package',
            feature: 'SomeFeature',
            installCommand: 'composer require vendor/another-package:^2.0'
        );

        $this->assertStringContainsString('vendor/another-package:^2.0', $exception->getMessage());
    }
}
