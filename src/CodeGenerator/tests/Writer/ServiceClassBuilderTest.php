<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Tests\Writer;

use Nette\PhpGenerator\ClassType;
use P8p\CodeGenerator\Model\ClassMetadata;
use P8p\CodeGenerator\Model\GroupVersionKind;
use P8p\CodeGenerator\Model\Operation;
use P8p\CodeGenerator\Model\Parameter;
use P8p\CodeGenerator\Model\Service;
use P8p\CodeGenerator\Model\VerbEnum;
use P8p\CodeGenerator\Writer\ServiceClassBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

class ServiceClassBuilderTest extends TestCase
{
    private ServiceClassBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new ServiceClassBuilder();
    }

    public function testBuildSimpleService(): void
    {
        $service = new Service('core.v1.pod');
        $service->setClassMetadata(new ClassMetadata('App\\Api\\Core\\V1\\PodApi', '/path/to/PodApi.php'));

        $operation = new Operation(
            operationId: 'listPods',
            name: 'list',
            path: '/api/v1/pods',
            verb: VerbEnum::GET,
            groupVersionKind: new GroupVersionKind('', 'v1', 'Pod'),
            type: 'list'
        );
        $service->addOperation($operation);

        $namespace = $this->builder->build($service);

        $this->assertSame('App\\Api\\Core\\V1', $namespace->getName());

        $classes = $namespace->getClasses();
        $this->assertArrayHasKey('PodApi', $classes);

        $class = $classes['PodApi'];
        $this->assertInstanceOf(ClassType::class, $class);
        $this->assertSame('P8p\\Client\\Api\\AbstractApi', $class->getExtends());
        $this->assertTrue($class->hasMethod('list'));
    }

    public function testBuildServiceWithPathParameters(): void
    {
        $service = new Service('core.v1.pod');
        $service->setClassMetadata(new ClassMetadata('App\\Api\\Core\\V1\\PodApi', '/path/to/PodApi.php'));

        $operation = new Operation(
            operationId: 'readPod',
            name: 'read',
            path: '/api/v1/namespaces/{namespace}/pods/{name}',
            verb: VerbEnum::GET,
            groupVersionKind: new GroupVersionKind('', 'v1', 'Pod'),
            type: 'read',
            description: null,
            pathParameters: [
                new Parameter('namespace', Type::string(), 'object name and auth scope'),
                new Parameter('name', Type::string(), 'name of the Pod'),
            ]
        );

        $service->addOperation($operation);

        $namespace = $this->builder->build($service);

        $classes = $namespace->getClasses();
        $class = $classes['PodApi'];
        $method = $class->getMethod('read');

        $parameters = $method->getParameters();
        $this->assertCount(3, $parameters); // namespace, name, queryParameters
        $this->assertArrayHasKey('namespace', $parameters);
        $this->assertArrayHasKey('name', $parameters);
        $this->assertArrayHasKey('queryParameters', $parameters);

        $this->assertSame('string', $parameters['namespace']->getType());
        $this->assertSame('string', $parameters['name']->getType());
    }

    public function testBuildServiceWithBodyParameter(): void
    {
        $service = new Service('core.v1.pod');
        $service->setClassMetadata(new ClassMetadata('App\\Api\\Core\\V1\\PodApi', '/path/to/PodApi.php'));

        $operation = new Operation(
            operationId: 'createPod',
            name: 'create',
            path: '/api/v1/namespaces/{namespace}/pods',
            verb: VerbEnum::POST,
            groupVersionKind: new GroupVersionKind('', 'v1', 'Pod'),
            type: 'create',
            description: null,
            pathParameters: [
                new Parameter('namespace', Type::string(), 'object name and auth scope'),
            ],
            queryParameters: [],
            bodyType: Type::object('App\\Schema\\Core\\V1\\Pod')
        );

        $service->addOperation($operation);

        $namespace = $this->builder->build($service);

        $classes = $namespace->getClasses();
        $class = $classes['PodApi'];
        $method = $class->getMethod('create');

        $parameters = $method->getParameters();
        $this->assertArrayHasKey('body', $parameters);
        $this->assertSame('App\\Schema\\Core\\V1\\Pod', $parameters['body']->getType());

        // Check that body type is in use statements
        $uses = $namespace->getUses();
        $this->assertContains('App\\Schema\\Core\\V1\\Pod', $uses);
    }

    public function testBuildServiceWithResponseType(): void
    {
        $service = new Service('core.v1.pod');
        $service->setClassMetadata(new ClassMetadata('App\\Api\\Core\\V1\\PodApi', '/path/to/PodApi.php'));

        $operation = new Operation(
            operationId: 'readPod',
            name: 'read',
            path: '/api/v1/namespaces/{namespace}/pods/{name}',
            verb: VerbEnum::GET,
            groupVersionKind: new GroupVersionKind('', 'v1', 'Pod'),
            type: 'read'
        );
        $operation->responseType = Type::object('App\\Schema\\Core\\V1\\Pod');

        $service->addOperation($operation);

        $namespace = $this->builder->build($service);

        $classes = $namespace->getClasses();
        $class = $classes['PodApi'];
        $method = $class->getMethod('read');

        $this->assertSame('P8p\\Client\\Response', $method->getReturnType());

        // Check that response type is in use statements
        $uses = $namespace->getUses();
        $this->assertContains('App\\Schema\\Core\\V1\\Pod', $uses);
    }

    public function testBuildServiceWithQueryParameters(): void
    {
        $service = new Service('core.v1.pod');
        $service->setClassMetadata(new ClassMetadata('App\\Api\\Core\\V1\\PodApi', '/path/to/PodApi.php'));

        $operation = new Operation(
            operationId: 'listPods',
            name: 'list',
            path: '/api/v1/pods',
            verb: VerbEnum::GET,
            groupVersionKind: new GroupVersionKind('', 'v1', 'Pod'),
            type: 'list',
            description: null,
            pathParameters: [],
            queryParameters: [
                new Parameter('labelSelector', Type::string(), 'A selector to restrict the list'),
                new Parameter('limit', Type::int(), 'limit is a maximum number of responses'),
            ]
        );

        $service->addOperation($operation);

        $namespace = $this->builder->build($service);

        $classes = $namespace->getClasses();
        $class = $classes['PodApi'];
        $method = $class->getMethod('list');

        $parameters = $method->getParameters();
        $this->assertArrayHasKey('queryParameters', $parameters);
        $this->assertSame('array', $parameters['queryParameters']->getType());
        $this->assertSame([], $parameters['queryParameters']->getDefaultValue());
    }

    public function testBuildServiceMethodBody(): void
    {
        $service = new Service('core.v1.pod');
        $service->setClassMetadata(new ClassMetadata('App\\Api\\Core\\V1\\PodApi', '/path/to/PodApi.php'));

        $operation = new Operation(
            operationId: 'createPod',
            name: 'create',
            path: '/api/v1/namespaces/{namespace}/pods',
            verb: VerbEnum::POST,
            groupVersionKind: new GroupVersionKind('', 'v1', 'Pod'),
            type: 'create',
            description: null,
            pathParameters: [
                new Parameter('namespace', Type::string(), null),
            ],
            queryParameters: [],
            bodyType: Type::object('App\\Schema\\Core\\V1\\Pod'),
            responseType: Type::object('App\\Schema\\Core\\V1\\Pod')
        );

        $service->addOperation($operation);

        $namespace = $this->builder->build($service);

        $classes = $namespace->getClasses();
        $class = $classes['PodApi'];
        $method = $class->getMethod('create');

        $body = $method->getBody();

        $this->assertStringContainsString('return $this->client->makeRequest(', $body);
        $this->assertStringContainsString("verb: 'POST',", $body);
        $this->assertStringContainsString("path: '/api/v1/namespaces/{namespace}/pods',", $body);
        $this->assertStringContainsString("pathParameters: ['namespace'=> \$namespace],", $body);
        $this->assertStringContainsString('responseClass: \\App\\Schema\\Core\\V1\\Pod::class,', $body);
        $this->assertStringContainsString('body: $body,', $body);
    }

    public function testBuildServiceWithMultipleOperations(): void
    {
        $service = new Service('core.v1.pod');
        $service->setClassMetadata(new ClassMetadata('App\\Api\\Core\\V1\\PodApi', '/path/to/PodApi.php'));

        $listOp = new Operation(
            operationId: 'listPods',
            name: 'list',
            path: '/api/v1/pods',
            verb: VerbEnum::GET,
            groupVersionKind: new GroupVersionKind('', 'v1', 'Pod'),
            type: 'list'
        );

        $createOp = new Operation(
            operationId: 'createPod',
            name: 'create',
            path: '/api/v1/namespaces/{namespace}/pods',
            verb: VerbEnum::POST,
            groupVersionKind: new GroupVersionKind('', 'v1', 'Pod'),
            type: 'create'
        );

        $service->addOperation($listOp);
        $service->addOperation($createOp);

        $namespace = $this->builder->build($service);

        $classes = $namespace->getClasses();
        $class = $classes['PodApi'];

        $this->assertTrue($class->hasMethod('list'));
        $this->assertTrue($class->hasMethod('create'));
    }

    public function testBuildServiceExtendsAbstractApi(): void
    {
        $service = new Service('core.v1.pod');
        $service->setClassMetadata(new ClassMetadata('App\\Api\\Core\\V1\\PodApi', '/path/to/PodApi.php'));

        $operation = new Operation(
            operationId: 'listPods',
            name: 'list',
            path: '/api/v1/pods',
            verb: VerbEnum::GET,
            groupVersionKind: new GroupVersionKind('', 'v1', 'Pod'),
            type: 'list'
        );
        $service->addOperation($operation);

        $namespace = $this->builder->build($service);

        $classes = $namespace->getClasses();
        $class = $classes['PodApi'];
        $this->assertInstanceOf(ClassType::class, $class);

        $this->assertSame('P8p\\Client\\Api\\AbstractApi', $class->getExtends());

        $uses = $namespace->getUses();
        $this->assertContains('P8p\\Client\\Api\\AbstractApi', $uses);
        $this->assertContains('P8p\\Client\\Response', $uses);
    }
}
