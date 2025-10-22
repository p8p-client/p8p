<?php

namespace P8p;

use P8p\Client\ClientFactory;
use P8p\Sdk\Api\Core\V1\PodApi;
use P8p\Sdk\Schema\Core\V1\Container;
use P8p\Sdk\Schema\Core\V1\ContainerPort;
use P8p\Sdk\Schema\Core\V1\Pod;
use P8p\Sdk\Schema\Core\V1\PodSpec;
use P8p\Sdk\Schema\Meta\V1\ObjectMeta;

include __DIR__.'/../src/Sdk/vendor/autoload.php';

$client = ClientFactory::fromUrl('http://127.0.0.1:8001')->getClient();;
$podsApi = $client->getApi(PodApi::class);

$pod = $podsApi->create('default', new Pod(
    metadata: new ObjectMeta(
        name: 'p8p-create-test',
    ),
    spec: new PodSpec(
        containers: [
            new Container(
                name: 'nginx',
                image: 'nginx',
                ports: [
                    new ContainerPort(
                        containerPort: 80,
                    )
                ],
            )
        ],
    )
))->getContent();

dd($pod);