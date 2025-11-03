<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\Api;

use P8p\Client\Attribute\K8sCustomResourceSchema;
use P8p\Client\CustomResource\CustomRessourceList;
use P8p\Client\Response;

/**
 * Generic API for managing Kubernetes Custom Resources.
 *
 * This class provides CRUD operations for any custom resource type
 *
 * @template T of object
 */
class CustomResourceApi extends AbstractApi
{
    private K8sCustomResourceSchema $metadata;

    /**
     * @param class-string<T> $resourceClass The custom resource class (must have #[K8sCustomResourceSchema] attribute)
     */
    public function __construct(
        private readonly string $resourceClass,
    ) {
        $this->metadata = $this->extractMetadata($resourceClass);
    }

    /**
     * List custom resources.
     *
     * @param string|null                                                                                                                                                                                                                                                                                                      $namespace       The namespace (required for namespaced resources, ignored for cluster-scoped)
     * @param array{pretty?: string|null, allowWatchBookmarks?: bool|null, continue?: string|null, fieldSelector?: string|null, labelSelector?: string|null, limit?: int|null, resourceVersion?: string|null, resourceVersionMatch?: string|null, sendInitialEvents?: bool|null, timeoutSeconds?: int|null, watch?: bool|null} $queryParameters
     *
     * @return Response<CustomRessourceList<T>>
     */
    public function list(?string $namespace = null, array $queryParameters = []): Response
    {
        $path = $this->metadata->namespaced && null !== $namespace
            ? '/apis/{group}/{version}/namespaces/{namespace}/{plural}'
            : '/apis/{group}/{version}/{plural}';

        $pathParameters = [
            'group' => $this->metadata->group,
            'version' => $this->metadata->version,
            'plural' => $this->metadata->plural,
        ];

        if ($this->metadata->namespaced && null !== $namespace) {
            $pathParameters['namespace'] = $namespace;
        }

        /* @var Response<CustomRessourceList<T>> */
        return $this->client->makeRequest( /* @phpstan-ignore argument.templateType */
            verb: 'GET',
            path: $path,
            pathParameters: $pathParameters,
            responseClass: sprintf('%s<%s>', CustomRessourceList::class, $this->resourceClass), /* @phpstan-ignore argument.type */
            queryParameters: $queryParameters,
        );
    }

    /**
     * Create a custom resource.
     *
     * @param T                                                                                                            $body
     * @param string|null                                                                                                  $namespace       The namespace (required for namespaced resources)
     * @param array{pretty?: string|null, dryRun?: string|null, fieldManager?: string|null, fieldValidation?: string|null} $queryParameters
     *
     * @return Response<T>
     */
    public function create(object $body, ?string $namespace = null, array $queryParameters = []): Response
    {
        $path = $this->metadata->namespaced && null !== $namespace
            ? '/apis/{group}/{version}/namespaces/{namespace}/{plural}'
            : '/apis/{group}/{version}/{plural}';

        $pathParameters = [
            'group' => $this->metadata->group,
            'version' => $this->metadata->version,
            'plural' => $this->metadata->plural,
        ];

        if ($this->metadata->namespaced && null !== $namespace) {
            $pathParameters['namespace'] = $namespace;
        }

        return $this->client->makeRequest(
            verb: 'POST',
            path: $path,
            pathParameters: $pathParameters,
            responseClass: $this->resourceClass,
            body: $body,
            queryParameters: $queryParameters,
        );
    }

    /**
     * Read a custom resource.
     *
     * @param string                      $name            The name of the resource
     * @param string|null                 $namespace       The namespace (required for namespaced resources)
     * @param array{pretty?: string|null} $queryParameters
     *
     * @return Response<T>
     */
    public function read(string $name, ?string $namespace = null, array $queryParameters = []): Response
    {
        $path = $this->metadata->namespaced && null !== $namespace
            ? '/apis/{group}/{version}/namespaces/{namespace}/{plural}/{name}'
            : '/apis/{group}/{version}/{plural}/{name}';

        $pathParameters = [
            'group' => $this->metadata->group,
            'version' => $this->metadata->version,
            'plural' => $this->metadata->plural,
            'name' => $name,
        ];

        if ($this->metadata->namespaced && null !== $namespace) {
            $pathParameters['namespace'] = $namespace;
        }

        return $this->client->makeRequest(
            verb: 'GET',
            path: $path,
            pathParameters: $pathParameters,
            responseClass: $this->resourceClass,
            queryParameters: $queryParameters,
        );
    }

    /**
     * Update a custom resource (full replacement).
     *
     * @param string                                                                                                       $name            The name of the resource
     * @param T                                                                                                            $body
     * @param string|null                                                                                                  $namespace       The namespace (required for namespaced resources)
     * @param array{pretty?: string|null, dryRun?: string|null, fieldManager?: string|null, fieldValidation?: string|null} $queryParameters
     *
     * @return Response<T>
     */
    public function replace(string $name, object $body, ?string $namespace = null, array $queryParameters = []): Response
    {
        $path = $this->metadata->namespaced && null !== $namespace
            ? '/apis/{group}/{version}/namespaces/{namespace}/{plural}/{name}'
            : '/apis/{group}/{version}/{plural}/{name}';

        $pathParameters = [
            'group' => $this->metadata->group,
            'version' => $this->metadata->version,
            'plural' => $this->metadata->plural,
            'name' => $name,
        ];

        if ($this->metadata->namespaced && null !== $namespace) {
            $pathParameters['namespace'] = $namespace;
        }

        return $this->client->makeRequest(
            verb: 'PUT',
            path: $path,
            pathParameters: $pathParameters,
            responseClass: $this->resourceClass,
            body: $body,
            queryParameters: $queryParameters,
        );
    }

    /**
     * Delete a custom resource.
     *
     * @param string                                                                                                                                                                                                        $name            The name of the resource
     * @param string|null                                                                                                                                                                                                   $namespace       The namespace (required for namespaced resources)
     * @param array{pretty?: string|null, dryRun?: string|null, gracePeriodSeconds?: int|null, ignoreStoreReadErrorWithClusterBreakingPotential?: bool|null, orphanDependents?: bool|null, propagationPolicy?: string|null} $queryParameters
     *
     * @return Response<mixed>
     */
    public function delete(string $name, ?string $namespace = null, array $queryParameters = []): Response
    {
        $path = $this->metadata->namespaced && null !== $namespace
            ? '/apis/{group}/{version}/namespaces/{namespace}/{plural}/{name}'
            : '/apis/{group}/{version}/{plural}/{name}';

        $pathParameters = [
            'group' => $this->metadata->group,
            'version' => $this->metadata->version,
            'plural' => $this->metadata->plural,
            'name' => $name,
        ];

        if ($this->metadata->namespaced && null !== $namespace) {
            $pathParameters['namespace'] = $namespace;
        }

        return $this->client->makeRequest( /* @phpstan-ignore argument.templateType */
            verb: 'DELETE',
            path: $path,
            pathParameters: $pathParameters,
            responseClass: null,
            queryParameters: $queryParameters,
        );
    }

    /**
     * Patch a custom resource (partial update).
     *
     * @param string                                                                                                                          $name            The name of the resource
     * @param array<mixed>                                                                                                                    $body            The patch data
     * @param string|null                                                                                                                     $namespace       The namespace (required for namespaced resources)
     * @param array{pretty?: string|null, dryRun?: string|null, fieldManager?: string|null, fieldValidation?: string|null, force?: bool|null} $queryParameters
     *
     * @return Response<T>
     */
    public function patch(string $name, array $body, ?string $namespace = null, array $queryParameters = []): Response
    {
        $path = $this->metadata->namespaced && null !== $namespace
            ? '/apis/{group}/{version}/namespaces/{namespace}/{plural}/{name}'
            : '/apis/{group}/{version}/{plural}/{name}';

        $pathParameters = [
            'group' => $this->metadata->group,
            'version' => $this->metadata->version,
            'plural' => $this->metadata->plural,
            'name' => $name,
        ];

        if ($this->metadata->namespaced && null !== $namespace) {
            $pathParameters['namespace'] = $namespace;
        }

        return $this->client->makeRequest(
            verb: 'PATCH',
            path: $path,
            pathParameters: $pathParameters,
            responseClass: $this->resourceClass,
            body: $body,
            queryParameters: $queryParameters,
        );
    }

    /**
     * Delete collection of custom resources.
     *
     * @param string|null                                                                                                                                                                                                                                                                                                                                                                                                                                    $namespace       The namespace (required for namespaced resources)
     * @param array{pretty?: string|null, continue?: string|null, dryRun?: string|null, fieldSelector?: string|null, gracePeriodSeconds?: int|null, ignoreStoreReadErrorWithClusterBreakingPotential?: bool|null, labelSelector?: string|null, limit?: int|null, orphanDependents?: bool|null, propagationPolicy?: string|null, resourceVersion?: string|null, resourceVersionMatch?: string|null, sendInitialEvents?: bool|null, timeoutSeconds?: int|null} $queryParameters
     *
     * @return Response<mixed>
     */
    public function deleteCollection(?string $namespace = null, array $queryParameters = []): Response
    {
        $path = $this->metadata->namespaced && null !== $namespace
            ? '/apis/{group}/{version}/namespaces/{namespace}/{plural}'
            : '/apis/{group}/{version}/{plural}';

        $pathParameters = [
            'group' => $this->metadata->group,
            'version' => $this->metadata->version,
            'plural' => $this->metadata->plural,
        ];

        if ($this->metadata->namespaced && null !== $namespace) {
            $pathParameters['namespace'] = $namespace;
        }

        return $this->client->makeRequest( /* @phpstan-ignore argument.templateType */
            verb: 'DELETE',
            path: $path,
            pathParameters: $pathParameters,
            responseClass: null,
            queryParameters: $queryParameters,
        );
    }

    /**
     * Extract CustomResource metadata from the resource class.
     *
     * @param class-string<T> $resourceClass
     *
     * @throws \InvalidArgumentException If the class doesn't have a K8sCustomResourceSchema attribute
     */
    private function extractMetadata(string $resourceClass): K8sCustomResourceSchema
    {
        $reflection = new \ReflectionClass($resourceClass);
        $attributes = $reflection->getAttributes(K8sCustomResourceSchema::class);

        if (empty($attributes)) {
            throw new \InvalidArgumentException(sprintf('Class %s must have a #[K8sCustomResourceSchema] attribute', $resourceClass));
        }

        return $attributes[0]->newInstance();
    }

    /**
     * Get the CustomResource metadata for this API.
     */
    public function getMetadata(): K8sCustomResourceSchema
    {
        return $this->metadata;
    }
}
