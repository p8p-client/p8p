<?php

namespace P8p;

use P8p\Client\ClientFactory;
use P8p\Client\CustomResource\CustomResourceDefinitionBuilder;
use P8p\Sdk\Api\Apiextensions\V1\CustomResourceDefinitionApi;
use P8p\Sdk\Schema\Meta\V1\DeleteOptions;

include __DIR__ . '/../../src/Sdk/vendor/autoload.php';
include __DIR__ . '/model.php';

$client = ClientFactory::fromKubeConfig(__DIR__ . '/../../../../.kube/config')->getClient();
$builder = new CustomResourceDefinitionBuilder();
$customResourceDefinitionApi = $client->getApi(CustomResourceDefinitionApi::class);

// Build CRD with both v1 and v2 (v2 will be the storage version)
$newCrd = $builder->build([CronTabV1::class, CronTabV2::class]);

// Strategy: try to read existing CRD to get its resourceVersion
$existingCrd = $customResourceDefinitionApi->read('crontabs.stable.example.com');

if ($existingCrd->isSuccessful()) {
    // CRD already exists, retrieve its resourceVersion for the update
    $existing = $existingCrd->getContent();
    $newCrd->metadata->resourceVersion = $existing->metadata->resourceVersion;
    dump('update');
    $rs = $customResourceDefinitionApi->replace('crontabs.stable.example.com', $newCrd);
} else {
    // CRD doesn't exist, create it
    dump('create');
    $rs = $customResourceDefinitionApi->create($newCrd);
}

//$rs = $customResourceDefinitionApi->read('crontabs.stable.example.com');
//$rs = $customResourceDefinitionApi->delete('crontabs.stable.example.com', new DeleteOptions());

if(!$rs->isSuccessful()) {
    dd($rs->getError());
}

dump($rs->getContent());

