<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace P8p\Client\Tests\Api;

use P8p\Client\Api\CustomResourceApi;
use P8p\Client\Attribute\K8sCustomResourceSchema;
use P8p\Client\Client;
use P8p\Client\CustomResource\CustomRessourceList;
use P8p\Client\Response;
use P8p\Client\Tests\Fixtures\CustomResource\TestClusterCustomResource;
use P8p\Client\Tests\Fixtures\CustomResource\TestCustomResource;
use PHPUnit\Framework\TestCase;

class CustomResourceApiTest extends TestCase
{
    /** @var Client&\PHPUnit\Framework\MockObject\MockObject */
    private Client $client;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
    }

    public function testExtractMetadataFromCustomResource(): void
    {
        $api = new CustomResourceApi(TestCustomResource::class);
        $api->setClient($this->client);

        $metadata = $api->getMetadata();

        $this->assertInstanceOf(K8sCustomResourceSchema::class, $metadata);
        $this->assertSame('TestResource', $metadata->kind);
        $this->assertSame('example.com', $metadata->group);
        $this->assertSame('v1', $metadata->version);
        $this->assertSame('testresources', $metadata->plural);
        $this->assertTrue($metadata->namespaced);
    }

    public function testThrowsExceptionWhenClassDoesNotHaveAttribute(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must have a #[K8sCustomResourceSchema] attribute');

        new CustomResourceApi(\stdClass::class);
    }

    public function testListNamespacedResources(): void
    {
        $api = new CustomResourceApi(TestCustomResource::class);
        $api->setClient($this->client);

        $this->client->expects($this->once())
            ->method('makeRequest')
            ->with(
                'GET',
                '/apis/{group}/{version}/namespaces/{namespace}/{plural}',
                ['group' => 'example.com', 'version' => 'v1', 'plural' => 'testresources', 'namespace' => 'default'],
                sprintf('%s<%s>', CustomRessourceList::class, TestCustomResource::class),
                null,
                ['labelSelector' => 'app=test']
            )
            ->willReturn($this->createMock(Response::class));

        $response = $api->list('default', ['labelSelector' => 'app=test']);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testListClusterScopedResources(): void
    {
        $api = new CustomResourceApi(TestClusterCustomResource::class);
        $api->setClient($this->client);

        $this->client->expects($this->once())
            ->method('makeRequest')
            ->with(
                'GET',
                '/apis/{group}/{version}/{plural}',
                ['group' => 'cluster.example.com', 'version' => 'v1beta1', 'plural' => 'clusterresources'],
                sprintf('%s<%s>', CustomRessourceList::class, TestClusterCustomResource::class),
                null,
                []
            )
            ->willReturn($this->createMock(Response::class));

        $response = $api->list(null);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testCreateNamespacedResource(): void
    {
        $api = new CustomResourceApi(TestCustomResource::class);
        $api->setClient($this->client);

        $resource = new TestCustomResource();

        $this->client->expects($this->once())
            ->method('makeRequest')
            ->with(
                'POST',
                '/apis/{group}/{version}/namespaces/{namespace}/{plural}',
                ['group' => 'example.com', 'version' => 'v1', 'plural' => 'testresources', 'namespace' => 'default'],
                TestCustomResource::class,
                $resource,
                []
            )
            ->willReturn($this->createMock(Response::class));

        $response = $api->create($resource, 'default');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testReadNamespacedResource(): void
    {
        $api = new CustomResourceApi(TestCustomResource::class);
        $api->setClient($this->client);

        $this->client->expects($this->once())
            ->method('makeRequest')
            ->with(
                'GET',
                '/apis/{group}/{version}/namespaces/{namespace}/{plural}/{name}',
                ['group' => 'example.com', 'version' => 'v1', 'plural' => 'testresources', 'namespace' => 'default', 'name' => 'my-resource'],
                TestCustomResource::class,
                null,
                []
            )
            ->willReturn($this->createMock(Response::class));

        $response = $api->read('my-resource', 'default');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testReadClusterScopedResource(): void
    {
        $api = new CustomResourceApi(TestClusterCustomResource::class);
        $api->setClient($this->client);

        $this->client->expects($this->once())
            ->method('makeRequest')
            ->with(
                'GET',
                '/apis/{group}/{version}/{plural}/{name}',
                ['group' => 'cluster.example.com', 'version' => 'v1beta1', 'plural' => 'clusterresources', 'name' => 'my-cluster-resource'],
                TestClusterCustomResource::class,
                null,
                []
            )
            ->willReturn($this->createMock(Response::class));

        $response = $api->read('my-cluster-resource');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testReplaceNamespacedResource(): void
    {
        $api = new CustomResourceApi(TestCustomResource::class);
        $api->setClient($this->client);

        $resource = new TestCustomResource();

        $this->client->expects($this->once())
            ->method('makeRequest')
            ->with(
                'PUT',
                '/apis/{group}/{version}/namespaces/{namespace}/{plural}/{name}',
                ['group' => 'example.com', 'version' => 'v1', 'plural' => 'testresources', 'namespace' => 'default', 'name' => 'my-resource'],
                TestCustomResource::class,
                $resource,
                []
            )
            ->willReturn($this->createMock(Response::class));

        $response = $api->replace('my-resource', $resource, 'default');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testDeleteNamespacedResource(): void
    {
        $api = new CustomResourceApi(TestCustomResource::class);
        $api->setClient($this->client);

        $this->client->expects($this->once())
            ->method('makeRequest')
            ->with(
                'DELETE',
                '/apis/{group}/{version}/namespaces/{namespace}/{plural}/{name}',
                ['group' => 'example.com', 'version' => 'v1', 'plural' => 'testresources', 'namespace' => 'default', 'name' => 'my-resource'],
                null,
                null,
                []
            )
            ->willReturn($this->createMock(Response::class));

        $response = $api->delete('my-resource', 'default');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testPatchNamespacedResource(): void
    {
        $api = new CustomResourceApi(TestCustomResource::class);
        $api->setClient($this->client);

        $patch = ['spec' => ['replicas' => 3]];

        $this->client->expects($this->once())
            ->method('makeRequest')
            ->with(
                'PATCH',
                '/apis/{group}/{version}/namespaces/{namespace}/{plural}/{name}',
                ['group' => 'example.com', 'version' => 'v1', 'plural' => 'testresources', 'namespace' => 'default', 'name' => 'my-resource'],
                TestCustomResource::class,
                $patch,
                []
            )
            ->willReturn($this->createMock(Response::class));

        $response = $api->patch('my-resource', $patch, 'default');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testDeleteCollectionNamespaced(): void
    {
        $api = new CustomResourceApi(TestCustomResource::class);
        $api->setClient($this->client);

        $this->client->expects($this->once())
            ->method('makeRequest')
            ->with(
                'DELETE',
                '/apis/{group}/{version}/namespaces/{namespace}/{plural}',
                ['group' => 'example.com', 'version' => 'v1', 'plural' => 'testresources', 'namespace' => 'default'],
                null,
                null,
                ['labelSelector' => 'app=test']
            )
            ->willReturn($this->createMock(Response::class));

        $response = $api->deleteCollection('default', ['labelSelector' => 'app=test']);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testDeleteCollectionClusterScoped(): void
    {
        $api = new CustomResourceApi(TestClusterCustomResource::class);
        $api->setClient($this->client);

        $this->client->expects($this->once())
            ->method('makeRequest')
            ->with(
                'DELETE',
                '/apis/{group}/{version}/{plural}',
                ['group' => 'cluster.example.com', 'version' => 'v1beta1', 'plural' => 'clusterresources'],
                null,
                null,
                []
            )
            ->willReturn($this->createMock(Response::class));

        $response = $api->deleteCollection();

        $this->assertInstanceOf(Response::class, $response);
    }
}
