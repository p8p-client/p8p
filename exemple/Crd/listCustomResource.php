<?php

namespace P8p;

use P8p\Client\ClientFactory;

include __DIR__ . '/../../src/Sdk/vendor/autoload.php';
include __DIR__ . '/model.php';

$client = ClientFactory::fromKubeConfig(__DIR__ . '/../../../../.kube/config')->getClient();

$customResourceDefinitionApi = $client->getCustomResourceApi(CronTabV1::class);


$rs = $customResourceDefinitionApi->list('default');

if(!$rs->isSuccessful()) {
    dd($rs->getError());
}

dump($rs->getContent());

