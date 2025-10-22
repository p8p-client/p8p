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
use P8p\Client\Exception\ClientException;

use function Symfony\Component\String\u;

class InClusterProvider implements CredentialsProviderInterface
{
    private const string DEFAULT_ENDPOINT = 'https://kubernetes.default.svc';
    private const string TOKEN_PATH = '/var/run/secrets/kubernetes.io/serviceaccount/token';
    private const string CA_PATH = '/var/run/secrets/kubernetes.io/serviceaccount/ca.crt';

    public function __construct(private readonly ?string $endpoint = null)
    {
    }

    public function getCredentials(): Credentials
    {
        return new Credentials(
            endpoint: $this->endpoint ?? self::DEFAULT_ENDPOINT,
            token: $this->getFileContent(self::TOKEN_PATH),
            caFile: self::CA_PATH
        );
    }

    protected function getFileContent(string $path): string
    {
        $content = file_get_contents($path);

        if (false === $content) {
            throw new ClientException('Unable to read file '.$path);
        }

        return u($content)->trim()->toString();
    }
}
