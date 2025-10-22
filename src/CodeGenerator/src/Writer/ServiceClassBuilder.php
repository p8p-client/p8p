<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Writer;

use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use P8p\Client\Api\AbstractApi;
use P8p\Client\Response;
use P8p\Client\WebSocket\WebSocketConnection;
use P8p\CodeGenerator\Model\Operation;
use P8p\CodeGenerator\Model\Parameter;
use P8p\CodeGenerator\Model\Service;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\TypeIdentifier;

use function Symfony\Component\String\u;

class ServiceClassBuilder
{
    public function build(Service $service): PhpNamespace
    {
        $namespace = new PhpNamespace($service->getClassMetadata()->getNamespace());
        $namespace->addUse(Response::class);
        $classType = $namespace->addClass($service->getClassMetadata()->getShortName());
        $classType->setExtends(AbstractApi::class);
        $namespace->addUse(AbstractApi::class);

        foreach ($service->getOperations() as $operation) {
            $method = $classType
                ->addMethod($operation->name)
                ->addComment(u($operation->description)->title())
                ->addComment('');

            foreach ($this->extractUse($operation) as $className) {
                $namespace->addUse($className);
            }

            foreach ($operation->pathParameters as $parameter) {
                $this->addMethodParameters($method, $parameter->name, $parameter->type, $parameter->description);
            }

            if ($operation->bodyType) {
                $phpParameter = $method->addParameter('body');
                $phpParameter->setType(TypeFormatter::toPhpType($operation->bodyType));
                $method->addComment('@param '.TypeFormatter::toPhpDocType($operation->bodyType).' $body');
            }

            $method->addParameter('queryParameters', [])->setType('array');
            $method->addComment("@param array{{$this->makeQueryParametersDoc($operation->queryParameters)}} \$queryParameters");

            $this->buildMethodBody($method, $operation);

            if ($operation->isConnectType()) {
                $namespace->addUse(WebSocketConnection::class);
                $method->setReturnType(WebSocketConnection::class);
            } else {
                $method->setReturnType(Response::class);
                if ($operation->responseType) {
                    $method->addComment('@return Response<'.TypeFormatter::toPhpDocType($operation->responseType).'>');
                }
            }
        }

        return $namespace;
    }

    private function addMethodParameters(Method $method, string $name, Type $type, ?string $description): void
    {
        $phpParameter = $method->addParameter($name);
        $phpParameter->setType(TypeFormatter::toPhpType($type));
        $method->addComment('@param '.TypeFormatter::toPhpDocType($type)." \${$name} {$description}");
    }

    private function buildMethodBody(Method $method, Operation $operation): void
    {
        if ($operation->isConnectType()) {
            // WebSocket connection for connect APIs
            $method->addBody('return $this->client->makeWebSocketConnection(');
            $method->addBody("    path: '{$operation->path}',");
            if ($operation->pathParameters) {
                $method->addBody("    pathParameters: [{$this->makeParametersArray($operation->pathParameters)}],");
            }
            if ($operation->queryParameters) {
                $method->addBody('    queryParameters: $queryParameters,');
            }
            $method->addBody(');');
        } else {
            // Standard HTTP request
            $method->addBody('return $this->client->makeRequest(');
            $method->addBody("    verb: '{$operation->verb->name}',");
            $method->addBody("    path: '{$operation->path}',");
            if ($operation->pathParameters) {
                $method->addBody("    pathParameters: [{$this->makeParametersArray($operation->pathParameters)}],");
            }
            if ($operation->responseType && $operation->responseType->isIdentifiedBy(TypeIdentifier::OBJECT)) {
                $method->addBody("    responseClass: \\{$operation->responseType}::class,");
            }
            if ($operation->bodyType) {
                $method->addBody('    body: $body,');
            }
            if ($operation->queryParameters) {
                $method->addBody('    queryParameters: $queryParameters,');
            }
            $method->addBody(');');
        }
    }

    /**
     * @param Parameter[] $parameters
     */
    private function makeParametersArray(array $parameters): string
    {
        return implode(', ', array_map(fn ($parameter) => "'{$parameter->name}'=> \${$parameter->name}", $parameters));
    }

    /**
     * @param Parameter[] $parameters
     */
    private function makeQueryParametersDoc(array $parameters): string
    {
        return implode(', ', array_map(fn ($parameter) => $parameter->name.'?: '.TypeFormatter::toPhpDocType($parameter->type), $parameters));
    }

    /**
     * @return \Generator<string>
     */
    private function extractUse(Operation $operation): \Generator
    {
        if ($operation->bodyType) {
            yield from TypeFormatter::extractAllClassNames($operation->bodyType);
        }

        if ($operation->responseType) {
            yield from TypeFormatter::extractAllClassNames($operation->responseType);
        }
    }
}
