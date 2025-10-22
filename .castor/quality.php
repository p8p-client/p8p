<?php

namespace quality;

use Castor\Attribute\AsTask;
use function Castor\run;
use function Castor\io;

#[AsTask()]
function all(): void
{
    csfixer();
    phpstan();
    rector();
}

#[AsTask]
function phpstan(): void
{
    io()->title('Run phpstan');
    run('vendor/bin/phpstan --memory-limit=1G');
}

#[AsTask()]
function csfixer(): void
{
    io()->title('Run php-cs-fixer');
    run('vendor/bin/php-cs-fixer fix');
}

#[AsTask]
function rector(): void
{
    io()->title('Run rector');
    run('vendor/bin/rector');
}
