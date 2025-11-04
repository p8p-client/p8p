<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client;

use P8p\Client\Exception\K8sApiException;
use P8p\Client\Exception\MissingDependencyException;
use P8p\Client\Serializer\Normalizer\WatchEventDenormalizer;
use P8p\Sdk\Schema\Meta\V1\Status;
use P8p\Sdk\Schema\Meta\V1\WatchEvent;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

/**
 * @template T
 */
class Response
{
    public function __construct(
        private readonly ResponseInterface $httpResponse,
        private readonly HttpClientInterface $httpClient,
        private readonly SerializerInterface $serializer,
        private readonly ?string $responseClass,
    ) {
    }

    public function isSuccessful(): bool
    {
        return $this->httpResponse->getStatusCode() < 400;
    }

    /** @return T|string */
    public function getContent(): mixed
    {
        try {
            $data = $this->httpResponse->getContent();
        } catch (HttpExceptionInterface $exception) {
            $errorContent = $this->httpResponse->getContent(false);
            /** @var array{message?: string}|null $errorData */
            $errorData = json_decode($errorContent, true);

            throw new K8sApiException($errorData['message'] ?? $exception->getMessage(), $this->httpResponse->getStatusCode(), $exception);
        }

        if ($this->responseClass) {
            return $this->serializer->deserialize($data, $this->responseClass, 'json');
        }

        return $data;
    }

    public function getError(): ?Status
    {
        if (!class_exists(Status::class)) {
            throw MissingDependencyException::forPackage(package: 'p8p/sdk', feature: 'Response::getError');
        }

        if ($this->isSuccessful()) {
            return null;
        }

        return $this->serializer->deserialize($this->httpResponse->getContent(false), Status::class, 'json');
    }

    public function stream(?float $timeout = null): ResponseStreamInterface
    {
        return $this->httpClient->stream($this->httpResponse, $timeout);
    }

    public function watch(?float $timeout = null, ?string $objectClass = null): \Generator
    {
        if (!class_exists(WatchEvent::class)) {
            throw new K8sApiException(sprintf('class "%s" is missing, You must install "p8p/sdk" package', WatchEvent::class));
        }

        foreach ($this->stream($timeout) as $chunk) {
            $parts = explode("\n", (string) $chunk->getContent());
            foreach ($parts as $part) {
                if ('' === $part) {
                    continue;
                }
                yield $this->serializer->deserialize($part, WatchEvent::class, 'json', [
                    WatchEventDenormalizer::WATCH_OBJECT_CLASS => $objectClass,
                ]);
            }
        }
    }
}
