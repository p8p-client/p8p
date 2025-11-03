<?php

namespace P8p;

use P8p\Client\ClientFactory;
use P8p\Sdk\Schema\Meta\V1\ObjectMeta;

include __DIR__ . '/../../src/Sdk/vendor/autoload.php';
include __DIR__ . '/model.php';

$client = ClientFactory::fromKubeConfig(__DIR__ . '/../../../../.kube/config')->getClient();

$customResourceDefinitionApi = $client->getCustomResourceApi(CronTabV2::class);

$crontab = new CronTabV2(
    metadata: new ObjectMeta(
        name: 'my-new-cron-object-v2',
    ),
    spec: new CronTabSpecV2(
        cronSpec: '*/1 * * * *',
        image: 'nginx',
        replicas: 1,
        command: 'toto'
    )
);

$rs = $customResourceDefinitionApi->create($crontab, 'default');

if(!$rs->isSuccessful()) {
    dd($rs->getError());
}

dump($rs->getContent());

