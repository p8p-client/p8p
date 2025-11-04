<?php

namespace tests;

use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;
use function Castor\run;
use function Castor\io;
use function Castor\context;


#[AsTask()]
function all(
    #[AsOption]
    bool $coverage = false,
): void
{
    client($coverage);
    generator($coverage);
    sdk();
}

#[AsTask]
function client(
    #[AsOption]
    bool $coverage = false,
): void
{
    io()->title('Run P8p\Client tests suite ');
    runTests('src/Client', $coverage);
}


#[AsTask]
function generator(
    #[AsOption]
    bool $coverage = false,
): void
{
    io()->title('Run P8p\CodeGenerator tests suite ');
    runTests('src/CodeGenerator', $coverage);
}


#[AsTask]
function sdk(): void
{
    io()->title('Run P8p\CodeGenerator sdk suite ');
    runTests('src/Sdk', false);
}


function runTests(string $path, bool $coverage): void
{
    $coverage = $coverage ? '--coverage-html coverage' : '';
    run(sprintf('XDEBUG_MODE=coverage php vendor/bin/phpunit %s', $coverage), context: context()->withTty()->withWorkingDirectory($path));
}