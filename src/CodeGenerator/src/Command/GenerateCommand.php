<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Command;

use P8p\CodeGenerator\Config\Config;
use P8p\CodeGenerator\Model\Model;
use P8p\CodeGenerator\Reader\OpenApiV3Reader;
use P8p\CodeGenerator\Writer\Cleaner;
use P8p\CodeGenerator\Writer\DocumentationGenerator;
use P8p\CodeGenerator\Writer\Writer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('generate')]
class GenerateCommand extends Command
{
    private const string DEFAULT_CONFIG_PATH = __DIR__.'/../../config.php';

    protected function configure(): void
    {
        $this->setDescription('Create or update API client methods.');
        $this->setDefinition([
            new InputArgument('baseUrl', InputArgument::OPTIONAL, 'K8S api url', 'http://127.0.0.1:8001/'),
            new InputOption('config', 'c', InputOption::VALUE_REQUIRED, 'Path to config file', self::DEFAULT_CONFIG_PATH),
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $configFile = $input->getOption('config');
        $url = rtrim((string) $input->getArgument('baseUrl'), '/'); /* @phpstan-ignore cast.string */

        if (!file_exists($configFile)) { /* @phpstan-ignore argument.type */
            throw new \Exception('Invalid path for config file !');
        }

        $config = require $configFile;

        if (!$config instanceof Config) {
            throw new \Exception('invalid config file, You must return a Config object !');
        }

        $io->title('Read OpenApi spec');

        $model = new Model();
        $openApiReader = new OpenApiV3Reader($url, $config);
        $openApiReader->read($model);

        $io->title('Write php files');

        $cleaner = new Cleaner($config);
        $cleaner->clean();

        $writer = new Writer();
        $writer->write($model);

        if (null !== $config->documentationOutputDir) {
            $io->title('Generate documentation');

            $docGenerator = new DocumentationGenerator($config);
            $docGenerator->generate($model);

            $io->writeln(sprintf(
                '<info>Documentation generated at: %s</info>',
                $config->documentationOutputDir
            ));
        }

        $io->success('Generation done !');

        return Command::SUCCESS;
    }
}
