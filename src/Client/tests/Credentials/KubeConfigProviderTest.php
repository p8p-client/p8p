<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Tests\Credentials;

use P8p\Client\Credentials\KubeConfigProvider;
use P8p\Client\Exception\KubeConfigException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class KubeConfigProviderTest extends TestCase
{
    private string $configPath;
    private string $cachePath;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->configPath = sys_get_temp_dir().'/test-kubeconfig-'.uniqid();
        $this->cachePath = sys_get_temp_dir().'/test-cache-'.uniqid();

        $config = <<<YAML
apiVersion: v1
kind: Config
clusters:
- cluster:
    server: https://my-cluster:6443
    certificate-authority-data: Y2EtY2VydA==
  name: my-cluster
contexts:
- context:
    cluster: my-cluster
    user: my-user
  name: my-context
users:
- name: my-user
  user:
    client-certificate-data: Y2xpZW50LWNlcnQ=
    client-key-data: Y2xpZW50LWtleQ==
current-context: my-context
YAML;

        $this->filesystem->dumpFile($this->configPath, $config);
        $this->filesystem->mkdir($this->cachePath);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove([$this->configPath, $this->cachePath]);
    }

    public function testGetCredentials(): void
    {
        if (!class_exists('Symfony\Component\Yaml\Yaml')) {
            $this->markTestSkipped('Symfony YAML component not installed');
        }

        $provider = new KubeConfigProvider(
            path: $this->configPath,
            cachePath: $this->cachePath,
        );

        $credentials = $provider->getCredentials();

        $this->assertEquals('https://my-cluster:6443', $credentials->endpoint);
        $this->assertNotNull($credentials->caFile);
        $this->assertNotNull($credentials->certificateFile);
        $this->assertNotNull($credentials->privateKeyFile);

        // Verify files were created
        $this->assertFileExists($credentials->caFile);
        $this->assertFileExists($credentials->certificateFile);
        $this->assertFileExists($credentials->privateKeyFile);

        // Verify content (base64 decoded)
        $this->assertEquals('ca-cert', file_get_contents($credentials->caFile));
        $this->assertEquals('client-cert', file_get_contents($credentials->certificateFile));
        $this->assertEquals('client-key', file_get_contents($credentials->privateKeyFile));
    }

    public function testGetCredentialsWithSpecificContext(): void
    {
        if (!class_exists('Symfony\Component\Yaml\Yaml')) {
            $this->markTestSkipped('Symfony YAML component not installed');
        }

        $provider = new KubeConfigProvider(
            path: $this->configPath,
            contextName: 'my-context',
            cachePath: $this->cachePath,
        );

        $credentials = $provider->getCredentials();

        $this->assertEquals('https://my-cluster:6443', $credentials->endpoint);
    }

    public function testGetCredentialsThrowsExceptionWhenNoContext(): void
    {
        if (!class_exists('Symfony\Component\Yaml\Yaml')) {
            $this->markTestSkipped('Symfony YAML component not installed');
        }

        // Create config without current-context
        $config = <<<YAML
apiVersion: v1
kind: Config
clusters:
- cluster:
    server: https://my-cluster:6443
  name: my-cluster
contexts:
- context:
    cluster: my-cluster
    user: my-user
  name: my-context
users:
- name: my-user
  user:
    client-certificate-data: Y2xpZW50LWNlcnQ=
    client-key-data: Y2xpZW50LWtleQ==
YAML;

        $configPath = sys_get_temp_dir().'/test-kubeconfig-no-context-'.uniqid();
        $this->filesystem->dumpFile($configPath, $config);

        try {
            $this->expectException(KubeConfigException::class);
            $this->expectExceptionMessage('No context name provided');

            $provider = new KubeConfigProvider(path: $configPath, cachePath: $this->cachePath);
            $provider->getCredentials();
        } finally {
            $this->filesystem->remove($configPath);
        }
    }
}
