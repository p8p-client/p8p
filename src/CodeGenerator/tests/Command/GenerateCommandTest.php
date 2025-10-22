<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Tests\Command;

use P8p\CodeGenerator\Command\GenerateCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateCommandTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir().'/p8p_command_test_'.uniqid();
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            $this->removeDirectory($this->tempDir);
        }
    }

    public function testCommandIsConfiguredCorrectly(): void
    {
        $command = new GenerateCommand();

        $this->assertSame('generate', $command->getName());
        $this->assertSame('Create or update API client methods.', $command->getDescription());

        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('baseUrl'));
        $this->assertTrue($definition->hasOption('config'));

        $baseUrlArg = $definition->getArgument('baseUrl');
        $this->assertFalse($baseUrlArg->isRequired());
        $this->assertSame('http://127.0.0.1:8001/', $baseUrlArg->getDefault());

        $configOpt = $definition->getOption('config');
        $this->assertTrue($configOpt->isValueRequired());
    }

    public function testExecuteThrowsExceptionWhenConfigFileDoesNotExist(): void
    {
        $command = new GenerateCommand();
        $commandTester = new CommandTester($command);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid path for config file !');

        $commandTester->execute([
            '--config' => '/path/to/nonexistent/config.php',
        ]);
    }

    public function testExecuteThrowsExceptionWhenConfigFileDoesNotReturnConfigObject(): void
    {
        $configFile = $this->tempDir.'/invalid_config.php';
        file_put_contents($configFile, '<?php return "not a config object";');

        $command = new GenerateCommand();
        $commandTester = new CommandTester($command);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('invalid config file, You must return a Config object !');

        $commandTester->execute([
            '--config' => $configFile,
        ]);
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir.'/'.$file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
