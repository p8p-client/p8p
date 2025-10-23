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

use P8p\CodeGenerator\Config\Config;
use Symfony\Component\Filesystem\Filesystem;

class Cleaner
{
    public function __construct(private readonly Config $config)
    {
    }

    public function clean(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->config->documentationOutputDir);
        $filesystem->remove($this->config->basePath);
    }
}
