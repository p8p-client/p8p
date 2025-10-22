<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Model;

use Symfony\Component\TypeInfo\Type;

class Operation
{
    /**
     * @param Parameter[] $pathParameters
     * @param Parameter[] $queryParameters
     */
    public function __construct(
        public string $operationId,
        public string $name,
        public string $path,
        public VerbEnum $verb,
        public GroupVersionKind $groupVersionKind,
        public ?string $type,
        public ?string $description = null,
        public array $pathParameters = [],
        public array $queryParameters = [],
        public ?Type $bodyType = null,
        public ?Type $responseType = null,
    ) {
    }

    public function isConnectType(): bool
    {
        return 'connect' === $this->type;
    }
}
