<?php

/*
 * This file is part of Monsieur Biz' Media Manager plugin for Sylius.
 *
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusMediaManagerPlugin\Twig\Extension;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Templating\FilterExtension as BaseFilterExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FilterExtensionDecorator extends BaseFilterExtension
{
    private array $loaders;

    private string $publicDir;

    public function __construct(
        CacheManager $cache,
        array $loaders,
        string $publicDir
    ) {
        parent::__construct($cache);
        $this->loaders = $loaders;
        $this->publicDir = $publicDir;
    }

    /**
     * Allow us to have multiple data roots for svg images.
     *
     * @param string $path
     * @param string $filter
     * @param string|null $resolver
     * @param int $referenceType
     */
    public function filter(
        $path,
        $filter,
        array $config = [],
        $resolver = null,
        $referenceType = UrlGeneratorInterface::ABSOLUTE_URL
    ) {
        $dataRoots = $this->loaders['default']['filesystem']['data_root'] ?? ['/media/image/'];

        foreach (array_unique($dataRoots) as $imagePath) {
            if (!$this->canImageBeFiltered($path) && file_exists(sprintf('%s/%s', $imagePath, $path))) {
                return str_replace($this->publicDir, '', sprintf('%s/%s', $imagePath, $path));
            }
        }

        /** @psalm-suppress DeprecatedClass */
        return parent::filter($path, $filter, $config, $resolver, $referenceType);
    }

    private function canImageBeFiltered(string $path): bool
    {
        return !str_ends_with($path, '.svg');
    }
}
