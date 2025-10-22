<?php

use Castor\Attribute\AsTask;
use function Castor\import;
use function Castor\io;
use function Castor\run;

import(__DIR__);

#[AsTask()]
function install(): void
{
    io()->title('Install dependencies');
    run('composer install');
    run('composer all install');
}
