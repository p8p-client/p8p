<?php

namespace generator;

use Castor\Attribute\AsTask;
use function Castor\context;
use function Castor\run;
use function Castor\io;
use function quality\csfixer;

#[AsTask()]
function generate(): void
{
    io()->title('Generate Sdk');

    run('php generate.php', context: context()->withTty()->withWorkingDirectory(__DIR__ . '/../src/CodeGenerator'));
    csfixer();
}

