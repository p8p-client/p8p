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

class UrlProvider implements CredentialsProviderInterface
{
    public function __construct(
        private readonly string $endpoint,
        private readonly ?string $token = null,
        private readonly ?string $caFile = null,
        private readonly ?string $certificationFile = null,
        private readonly ?string $privateKeyFile = null,
        private readonly ?string $httpUser = null,
        private readonly ?string $httpPassword = null,
    ) {
    }

    public function getCredentials(): Credentials
    {
        return new Credentials(
            endpoint: $this->endpoint,
            token: $this->token,
            caFile: $this->caFile,
            certificateFile: $this->certificationFile,
            privateKeyFile: $this->privateKeyFile,
            httpUser: $this->httpUser,
            httpPassword: $this->httpPassword
        );
    }
}
