<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Credentials;

use P8p\Client\Credentials;
use P8p\Client\Credentials\KubeConfig\KubeConfigLoader;
use P8p\Client\Exception\KubeConfigException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class KubeConfigProvider implements CredentialsProviderInterface
{
    private readonly KubeConfigLoader $loader;

    private readonly string $cachePath;

    public function __construct(
        private readonly string $path,
        private readonly ?string $contextName = null,
        ?string $cachePath = null,
    ) {
        $this->loader = new KubeConfigLoader();
        $this->cachePath = $cachePath ?? sys_get_temp_dir();
    }

    public function getCredentials(): Credentials
    {
        $kubeConfig = $this->loader->load($this->path);
        $contextName = $this->contextName ?? $kubeConfig->currentContext;

        if (null === $contextName) {
            throw new KubeConfigException('No context name provided and no current-context set in kubeconfig');
        }

        $context = $kubeConfig->getContext($contextName);
        $server = $kubeConfig->getCluster($context->cluster);
        $user = $kubeConfig->getUser($context->user);

        $caFile = $server->certificateAuthorityData ? $this->dumpFile($server->certificateAuthorityData, $contextName, 'ca-cert.pem') : null;
        $certificateFile = $user->clientCertificateData ? $this->dumpFile($user->clientCertificateData, $contextName, 'client-cert.pem') : null;
        $privateKeyFile = $user->clientKeyData ? $this->dumpFile($user->clientKeyData, $contextName, 'client-key.pem') : null;

        return new Credentials(
            endpoint: $server->server,
            caFile: $caFile,
            certificateFile: $certificateFile,
            privateKeyFile: $privateKeyFile,
        );
    }

    private function dumpFile(string $content, string $contextName, string $filename): string
    {
        $filePath = Path::join($this->cachePath, 'p8p', 'kubeConfig', 'credentials', $contextName, $filename);
        $filesystem = new Filesystem();
        $filesystem->dumpFile($filePath, base64_decode($content));

        return $filePath;
    }
}
