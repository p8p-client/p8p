<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\Client\CustomResource;

use P8p\Sdk\Schema\Meta\V1\ObjectMeta;

interface CustomResourceInterface
{
    public ObjectMeta $metadata {get; set; }
}
