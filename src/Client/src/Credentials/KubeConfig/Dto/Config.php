<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Credentials\KubeConfig\Dto;

use P8p\Client\Exception\KubeConfigException;

readonly class Config
{
    /**
     * @param array<Cluster> $clusters
     * @param array<Context> $contexts
     * @param array<User>    $users
     */
    public function __construct(
        public array $clusters = [],
        public array $contexts = [],
        public array $users = [],
        public ?string $currentContext = null,
    ) {
    }

    public function getCluster(string $name): Cluster
    {
        foreach ($this->clusters as $cluster) {
            if ($name === $cluster->name) {
                return $cluster;
            }
        }

        throw new KubeConfigException(sprintf('Cluster "%s" not found', $name));
    }

    public function getContext(string $name): Context
    {
        foreach ($this->contexts as $context) {
            if ($name === $context->name) {
                return $context;
            }
        }

        throw new KubeConfigException(sprintf('Context "%s" not found', $name));
    }

    public function getUser(string $name): User
    {
        foreach ($this->users as $user) {
            if ($name === $user->name) {
                return $user;
            }
        }

        throw new KubeConfigException(sprintf('User "%s" not found', $name));
    }
}
