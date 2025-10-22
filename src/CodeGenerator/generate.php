<?php

use P8p\CodeGenerator\Command\GenerateCommand;
use Symfony\Component\Console\Application;

include __DIR__.'/vendor/autoload.php';

// ignore php-openapi with php 8.4 (https://github.com/cebe/php-openapi/issues/245)
set_error_handler(
    function($code, $error, $file) {
        return str_contains($file, '/vendor/cebe/php-openapi/');
    },
    E_DEPRECATED
);

$application = new Application('p8p', '0.1.0');

$command = new GenerateCommand();
$application->add($command);
$application->setDefaultCommand($command->getName(), true);

$application->run();