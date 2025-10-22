<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Reader;

use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Operation as OperationSpec;
use cebe\openapi\spec\Parameter as ParameterSpec;
use cebe\openapi\spec\PathItem;
use cebe\openapi\spec\Schema as SchemaSpec;
use P8p\CodeGenerator\Config\Config;
use P8p\CodeGenerator\Exception\ReaderException;
use P8p\CodeGenerator\Model\GroupVersionKind;
use P8p\CodeGenerator\Model\Model;
use P8p\CodeGenerator\Model\Operation;
use P8p\CodeGenerator\Model\Parameter;
use P8p\CodeGenerator\Model\Property;
use P8p\CodeGenerator\Model\Schema;
use P8p\CodeGenerator\Model\Service;
use P8p\CodeGenerator\Model\VerbEnum;
use Symfony\Component\TypeInfo\Type;

use function Symfony\Component\String\u;

class OpenApiV3Reader
{
    public const string OPENAPI_PATH = 'openapi/v3';

    private readonly ClassMetadataExtractor $classMetadataExtractor;
    private readonly TypeExtractor $typeExtractor;

    public function __construct(
        private readonly string $baseUrl,
        private readonly Config $config,
    ) {
        $this->classMetadataExtractor = new ClassMetadataExtractor($config);
        $this->typeExtractor = new TypeExtractor($config);
    }

    public function read(Model $model): void
    {
        foreach ($this->config->apis as $api) {
            $apiPath = $api->isCore()
                ? sprintf('%s/%s/api/%s', $this->baseUrl, self::OPENAPI_PATH, $api->version)
                : sprintf('%s/%s/apis/%s/%s', $this->baseUrl, self::OPENAPI_PATH, $api->group, $api->version);

            $spec = Reader::readFromJsonFile($apiPath);
            $this->resolveSchemas($spec, $model);
            $this->resolveService($spec, $model);
        }
    }

    protected function resolveSchemas(OpenApi $openApi, Model $model): void
    {
        $schemas = $openApi->components->schemas ?? [];

        /** @var SchemaSpec $schemaSpecs */
        foreach ($schemas as $name => $schemaSpecs) {
            if ($model->hasSchema($name) || isset($this->config->schemasOverride[$name])) {
                continue;
            }

            $schema = new Schema($name);
            $schema->setDescription($schemaSpecs->description);
            $schema->setGroupVersionKind($this->createGroupVersionKind($schemaSpecs));
            $schema->setClassMetadata($this->classMetadataExtractor->extractForSchema($schemaSpecs));
            $model->addSchema($schema);
        }

        /** @var SchemaSpec $schemaSpecs */
        foreach ($schemas as $name => $schemaSpecs) {
            if ($model->hasSchema($name)) {
                $schema = $model->getSchema($name);
                foreach ($schemaSpecs->properties as $propertyName => $propertySpec) {
                    $type = $this->typeExtractor->extract($propertySpec, $model);
                    if (!in_array($propertyName, $schemaSpecs->required ?? [])) {
                        $type = Type::nullable($type);
                    }
                    $property = new Property($propertyName, $propertySpec->description, $type); /* @phpstan-ignore property.notFound */

                    $schema->addProperty($property);
                }

                $schema->reorderProperties();
            }
        }
    }

    protected function resolveService(OpenApi $openApi, Model $model): void
    {
        foreach ($openApi->paths as $path => $pathSpecs) {
            foreach ($pathSpecs->getOperations() as $operationSpec) {
                if (!$this->createGroupVersionKind($operationSpec)) {
                    continue;
                }

                $serviceClassMetadata = $this->classMetadataExtractor->extractForService($operationSpec);
                $serviceName = u($serviceClassMetadata->name)->replace('\\', '.')->lower();

                if (!$model->hasService($serviceName)) {
                    $service = new Service($serviceName);
                    $service->setClassMetadata($serviceClassMetadata);
                    $model->addService($service);
                }

                $operation = $this->createOperation($operationSpec, $path, $pathSpecs, $model);

                // deprecated operations
                if (in_array($operation->type, ['watch', 'watchlist'])) {
                    continue;
                }

                $model->getService($serviceName)->addOperation($operation);
            }
        }
    }

    private function createOperation(OperationSpec $operationSpecs, string $path, PathItem $pathSpecs, Model $model): Operation
    {
        $part = $operationSpecs->getDocumentPosition()?->getPath() ?? [];
        $verb = VerbEnum::from(end($part));
        $extensions = $operationSpecs->getExtensions();

        if (!$groupVersionKind = $this->createGroupVersionKind($operationSpecs)) {
            throw new ReaderException(sprintf('Unable to extract group version kind for operation "%s"', $operationSpecs->operationId));
        }

        $operation = new Operation(
            operationId: $operationSpecs->operationId,
            name: $this->generateOperationName($operationSpecs),
            path: $path,
            verb: $verb,
            groupVersionKind: $groupVersionKind,
            type: is_string($extensions['x-kubernetes-action']) ? $extensions['x-kubernetes-action'] : null,
            description: $operationSpecs->description
        );

        /** @var ParameterSpec[] $specParameters */
        $specParameters = array_merge($pathSpecs->parameters, $operationSpecs->parameters);
        foreach ($specParameters as $specParameter) {
            $parameter = new Parameter(
                $specParameter->name,
                $this->typeExtractor->extract($specParameter->schema, $model), /* @phpstan-ignore argument.type */
                $specParameter->description,
            );

            if ('path' === $specParameter->in) {
                $operation->pathParameters[] = $parameter;
            }
            if ('query' === $specParameter->in) {
                $parameter->type = Type::nullable($parameter->type);
                $operation->queryParameters[] = $parameter;
            }
        }

        $operation->bodyType = $this->extractRequestBodyType($operationSpecs, $model);
        $operation->responseType = $this->extractResponseType($operationSpecs, $model);

        return $operation;
    }

    private function createGroupVersionKind(SchemaSpec|OperationSpec $spec): ?GroupVersionKind
    {
        $extensions = $spec->getExtensions();
        if (!isset($extensions['x-kubernetes-group-version-kind'])) {
            return null;
        }

        /** @var array<string, string> $data */
        $data = $spec instanceof SchemaSpec ? $extensions['x-kubernetes-group-version-kind'][0] : $extensions['x-kubernetes-group-version-kind']; /* @phpstan-ignore offsetAccess.nonOffsetAccessible */

        return new GroupVersionKind(
            $data['group'],
            $data['version'],
            $data['kind'],
        );
    }

    private function generateOperationName(OperationSpec $operationSpec): string
    {
        $groupVersionKind = $this->createGroupVersionKind($operationSpec);

        if (!$groupVersionKind) {
            throw new ReaderException(sprintf('Unable to extract group version kind for operation "%s"', $operationSpec->operationId));
        }

        $cleanGroup = '' === $groupVersionKind->group ? 'core' : $groupVersionKind->group;
        $cleanGroup = u($cleanGroup)->replace('k8s.io', '')->camel()->title();
        $cleanVersion = u($groupVersionKind->version)->camel()->title();
        $cleanKind = u($groupVersionKind->kind)->camel()->title();

        return u($operationSpec->operationId)
            ->replace($cleanGroup.$cleanVersion, '')
            ->replace($cleanKind, '')
            ->replace('Namespaced', '')
            ->toString();
    }

    private function extractRequestBodyType(OperationSpec $operationSpec, Model $model): ?Type
    {
        if (!$operationSpec->requestBody) {
            return null;
        }

        if (isset($operationSpec->requestBody->content['application/json'])) {
            $requestSpec = $operationSpec->requestBody->content['application/json']->schema;
        } elseif (isset($operationSpec->requestBody->content['application/merge-patch+json'])) {
            $requestSpec = $operationSpec->requestBody->content['application/merge-patch+json']->schema;
        } elseif (isset($operationSpec->requestBody->content['*/*'])) {
            $requestSpec = $operationSpec->requestBody->content['*/*']->schema;
        } else {
            return null;
        }

        return $this->typeExtractor->extract($requestSpec, $model);
    }

    private function extractResponseType(OperationSpec $operationSpecs, Model $model): ?Type
    {
        $responses = $operationSpecs->responses ? iterator_to_array($operationSpecs->responses->getIterator()) : [];
        if (0 === count($responses)) {
            return null;
        }

        $response = current($responses);
        if (isset($response->content['application/json'])) {
            $responseSpec = $response->content['application/json']->schema;
        } elseif (isset($response->content['*/*'])) {
            $responseSpec = $response->content['*/*']->schema;
        } else {
            return null;
        }

        return $this->typeExtractor->extract($responseSpec, $model);
    }
}
