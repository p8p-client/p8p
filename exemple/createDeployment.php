<?php

namespace P8p;

use P8p\Client\ClientFactory;
use P8p\Sdk\Api\Apps\V1\DeploymentApi;
use P8p\Sdk\Api\Core\V1\PodApi;
use P8p\Sdk\Schema\Apps\V1\Deployment;
use P8p\Sdk\Schema\Apps\V1\DeploymentSpec;
use P8p\Sdk\Schema\Core\V1\Container;
use P8p\Sdk\Schema\Core\V1\ContainerPort;
use P8p\Sdk\Schema\Core\V1\Pod;
use P8p\Sdk\Schema\Core\V1\PodSpec;
use P8p\Sdk\Schema\Core\V1\PodTemplateSpec;
use P8p\Sdk\Schema\Meta\V1\LabelSelector;
use P8p\Sdk\Schema\Meta\V1\ObjectMeta;

include __DIR__.'/../src/Sdk/vendor/autoload.php';

$client = ClientFactory::fromUrl('http://127.0.0.1:8001')->getClient();;
$deploymentApi = $client->getApi(DeploymentApi::class);

$rs = $deploymentApi->create('default', new Deployment(
    metadata: new ObjectMeta(
        labels: [
            'app' => 'nginx',
        ],
        name: 'p8p-nginx-deployment'
    ),
    spec: new DeploymentSpec(
        selector: new LabelSelector(
            matchLabels: [
                'app' => 'nginx',
            ]
        ),
        template: new PodTemplateSpec(
            metadata: new ObjectMeta(
                labels: [
                    'app' => 'nginx',
                ]
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
                        ]
                    )
                ]
            )
        ),
        replicas: 3
    )
));

if(!$rs->isSuccessful()) {
    dd($rs->getError());
}

dd($rs->getContent());
