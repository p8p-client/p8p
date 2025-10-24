<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Credentials\KubeConfig;

use P8p\Client\Credentials\KubeConfig\Dto\Cluster;
use P8p\Client\Credentials\KubeConfig\Dto\Config;
use P8p\Client\Credentials\KubeConfig\Dto\Context;
use P8p\Client\Credentials\KubeConfig\Dto\User;
use P8p\Client\Exception\MissingDependencyException;
use Symfony\Component\Yaml\Yaml;

class KubeConfigLoader
{
    public function load(string $path): Config
    {
        if (!class_exists(Yaml::class)) {
            throw MissingDependencyException::forPackage(package: 'symfony/yaml', feature: 'KubeConfigLoader::load');
        }

        $data = Yaml::parseFile($path);

        if (!\is_array($data)) {
            throw new \InvalidArgumentException('Invalid kubeconfig file format');
        }

        return new Config(
            $this->getCluster($this->extractArray($data, 'clusters')),
            $this->getContexts($this->extractArray($data, 'contexts')),
            $this->getUsers($this->extractArray($data, 'users')),
            $this->extractNullableString($data, 'current-context'),
        );
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<Cluster>
     */
    private function getCluster(array $data): array
    {
        $clusters = [];

        foreach ($data as $clusterData) {
            if (!\is_array($clusterData)) {
                continue;
            }

            $cluster = $this->extractArray($clusterData, 'cluster');

            $clusters[] = new Cluster(
                $this->extractString($cluster, 'server'),
                $this->extractString($clusterData, 'name'),
                $this->extractNullableString($cluster, 'certificate-authority-data'),
            );
        }

        return $clusters;
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<Context>
     */
    private function getContexts(array $data): array
    {
        $contexts = [];

        foreach ($data as $contextData) {
            if (!\is_array($contextData)) {
                continue;
            }

            $context = $this->extractArray($contextData, 'context');

            $contexts[] = new Context(
                $this->extractString($contextData, 'name'),
                $this->extractString($context, 'cluster'),
                $this->extractString($context, 'user'),
            );
        }

        return $contexts;
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<User>
     */
    private function getUsers(array $data): array
    {
        $users = [];

        foreach ($data as $userData) {
            if (!\is_array($userData)) {
                continue;
            }

            $user = $this->extractArray($userData, 'user');

            $users[] = new User(
                $this->extractString($userData, 'name'),
                $this->extractNullableString($user, 'client-certificate-data'),
                $this->extractNullableString($user, 'client-key-data'),
            );
        }

        return $users;
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    private function extractArray(array $data, string $key): array
    {
        return \is_array($data[$key] ?? null) ? $data[$key] : [];
    }

    /**
     * @param array<mixed> $data
     */
    private function extractString(array $data, string $key, string $default = ''): string
    {
        $value = $data[$key] ?? null;

        return \is_string($value) ? $value : $default;
    }

    /**
     * @param array<mixed> $data
     */
    private function extractNullableString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return \is_string($value) ? $value : null;
    }
}
