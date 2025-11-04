<?php

namespace bundle;

use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;
use function Castor\run;
use function Castor\io;

#[AsTask()]
function devServer(
    #[AsOption]
    int $port = 8080,
): void
{
    io()->title('Run test application');
    io()->info(sprintf(' => http://localhost:%s', $port));
    io()->info('Press Ctrl+C to stop the server');

    run(sprintf('php -S localhost:%s -t src/Bundle/tests/TestApplication/public/', $port));
}

