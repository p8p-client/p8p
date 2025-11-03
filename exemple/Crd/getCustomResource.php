<?php

namespace P8p;

use P8p\Client\ClientFactory;

include __DIR__ . '/../../src/Sdk/vendor/autoload.php';
include __DIR__ . '/model.php';

$client = ClientFactory::fromKubeConfig(__DIR__ . '/../../../../.kube/config')->getClient();

$customResourceDefinitionApi = $client->getCustomResourceApi(CronTabV2::class);


$rs = $customResourceDefinitionApi->read('my-new-cron-object', 'default');

if(!$rs->isSuccessful()) {
    dd($rs->getError());
}

dump($rs->getContent());

