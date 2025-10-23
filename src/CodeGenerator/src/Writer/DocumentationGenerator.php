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

use P8p\CodeGenerator\Config\Config;
use P8p\CodeGenerator\Model\Model;
use P8p\CodeGenerator\Model\Service;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

use function Symfony\Component\String\u;

class DocumentationGenerator
{
    private readonly Environment $twig;
    private readonly Filesystem $filesystem;

    public function __construct(
        private readonly Config $config,
    ) {
        $loader = new FilesystemLoader($this->config->documentationTemplateDir);
        $this->twig = new Environment($loader);
        $this->filesystem = new Filesystem();

        $this->twig->addFilter(new TwigFilter('camel', $this->camelCase(...)));
        $this->twig->addFilter(new TwigFilter('table_cell', $this->tableCell(...), ['is_safe' => ['html']]));
    }

    public function generate(Model $model): void
    {
        // Generate a file for each service
        foreach ($model->getServices() as $service) {
            $this->generateServiceFile($service);
        }

        // Generate index file
        $this->generateIndexFile($model);
    }

    private function generateServiceFile(Service $service): void
    {
        $content = $this->twig->render('service.md.twig', [
            'service' => $service,
        ]);

        $filename = $this->getServiceFilename($service);
        $this->filesystem->dumpFile($this->config->documentationOutputDir.'/'.$filename, $content);
    }

    private function generateIndexFile(Model $model): void
    {
        $data = [];

        foreach ($model->getServices() as $service) {
            $group = $service->getGroupVersionKind()->group;
            $group = '' === $group ? 'Core' : $group;

            $data[$group][] = [
                'name' => $service->getClassMetadata()->getShortName(),
                'path' => $this->getServiceFilename($service),
                'version' => $service->getGroupVersionKind()->version,
            ];
        }

        // Sort services alphabetically within each group
        foreach ($data as &$services) {
            usort($services, fn ($a, $b) => $a['name'] <=> $b['name']);
        }

        $content = $this->twig->render('index.md.twig', [
            'data' => $data,
        ]);

        $this->filesystem->dumpFile($this->config->documentationOutputDir.'/index.md', $content);
    }

    private function getServiceFilename(Service $service): string
    {
        $shortName = u($service->getClassMetadata()->getShortName())->kebab()->toString();
        $group = '' === $service->getGroupVersionKind()->group ? 'core' : $service->getGroupVersionKind()->group;
        $version = $service->getGroupVersionKind()->version;

        return Path::join($group, $version, $shortName.'.md');
    }

    private function camelCase(string $string): string
    {
        return u($string)->camel()->toString();
    }

    /**
     * Sanitize a string for safe use in a markdown table cell.
     */
    private function tableCell(?string $string): string
    {
        if (null === $string || '' === $string) {
            return '';
        }

        $string = trim($string);

        // Replace newlines with <br>
        $string = str_replace(["\r\n", "\r", "\n"], '<br>', $string);

        // Escape pipe characters to prevent breaking table structure
        return str_replace('|', '\|', $string);
    }
}
