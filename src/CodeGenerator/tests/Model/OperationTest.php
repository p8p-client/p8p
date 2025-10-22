<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Tests\Model;

use P8p\CodeGenerator\Model\GroupVersionKind;
use P8p\CodeGenerator\Model\Operation;
use P8p\CodeGenerator\Model\Parameter;
use P8p\CodeGenerator\Model\VerbEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

class OperationTest extends TestCase
{
    public function testConstructorWithAllArguments(): void
    {
        $gvk = new GroupVersionKind('core', 'v1', 'Pod');
        $pathParam = new Parameter('namespace', Type::string(), 'The namespace');
        $queryParam = new Parameter('limit', Type::int(), 'Max results');
        $bodyType = Type::object('Pod');
        $responseType = Type::object('PodList');

        $operation = new Operation(
            operationId: 'createPod',
            name: 'create',
            path: '/api/v1/namespaces/{namespace}/pods',
            verb: VerbEnum::POST,
            groupVersionKind: $gvk,
            type: 'create',
            description: 'Create a new Pod',
            pathParameters: [$pathParam],
            queryParameters: [$queryParam],
            bodyType: $bodyType,
            responseType: $responseType
        );

        $this->assertSame('createPod', $operation->operationId);
        $this->assertSame('create', $operation->name);
        $this->assertSame('/api/v1/namespaces/{namespace}/pods', $operation->path);
        $this->assertSame(VerbEnum::POST, $operation->verb);
        $this->assertSame($gvk, $operation->groupVersionKind);
        $this->assertSame('create', $operation->type);
        $this->assertSame('Create a new Pod', $operation->description);
        $this->assertCount(1, $operation->pathParameters);
        $this->assertSame($pathParam, $operation->pathParameters[0]);
        $this->assertCount(1, $operation->queryParameters);
        $this->assertSame($queryParam, $operation->queryParameters[0]);
        $this->assertSame($bodyType, $operation->bodyType);
        $this->assertSame($responseType, $operation->responseType);
    }
}
