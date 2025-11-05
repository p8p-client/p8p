<?php

/*
 * This file is part of the P8P project.
 *
 * (c) Julien Jacottet <jjacottet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P8p\CodeGenerator\Config;

use Symfony\Component\TypeInfo\Type;

readonly class Config
{
    public readonly string $documentationTemplateDir;

    /**
     * @param Api[]               $apis
     * @param array<string, Type> $schemasOverride
     * @param ?string             $documentationOutputDir   Path to documentation output directory. Set to null to disable documentation generation.
     * @param ?string             $externalSdkPath          Path to external SDK source directory (e.g., __DIR__.'/../../vendor/p8p/sdk/src'). When set, the generator will scan this SDK and avoid duplicating types that exist in it.
     * @param ?string             $documentationTemplateDir Path to documentation templates directory. Defaults to built-in templates.
     */
    public function __construct(
        public string $baseNamespace,
        public string $basePath,
        public array $apis,
        public ?array $schemasOverride = [],
        public ?string $documentationOutputDir = null,
        public ?string $externalSdkPath = null,
        ?string $documentationTemplateDir = null,
    ) {
        $this->documentationTemplateDir = $documentationTemplateDir ?? __DIR__.'/../../templates/documentation';
    }
}
