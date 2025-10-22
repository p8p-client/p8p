<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__.'/src/*/src',
        __DIR__.'/src/*/tests',
    ])
;


$header = <<<'HEADER'
This file is part of the P8P project.

(c) Julien Jacottet <jjacottet@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        '@Symfony' => true,
        'header_comment' => [
            'header' => $header,
            'location' => 'after_open',
        ],
    ])
    ->setFinder($finder)
;
