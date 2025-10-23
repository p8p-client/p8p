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

use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;
use Symfony\Component\Filesystem\Filesystem;

class ClassDumper
{
    private readonly Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function print(PhpNamespace $namespace, string $path): void
    {
        $header = <<<'HEADER'
This file is part of the P8P project.

(c) Julien Jacottet <jjacottet@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

        $file = new PhpFile();
        $file->addComment($header);
        $file->setStrictTypes();
        $file->addNamespace($namespace);

        $printer = new PsrPrinter();
        $this->filesystem->dumpFile($path, $printer->printFile($file));
    }
}
