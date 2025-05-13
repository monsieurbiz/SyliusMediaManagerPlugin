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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Resolver;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Path;

readonly class FilePathResolver implements FilePathResolverInterface
{
    public function __construct(
        #[Autowire(env: 'resolve:MONSIEURBIZ_SYLIUS_MEDIA_MANAGER_PUBLIC_FOLDER')]
        private string $absolutePublicDirectoryPath,
        #[Autowire(env: 'resolve:MONSIEURBIZ_SYLIUS_MEDIA_MANAGER_ROOT_FOLDER_FROM_PUBLIC')]
        private string $relativeMediaDirectoryPath,
    ) {
    }

    public function getAbsoluteFilePath(string $relativeFilePath): string
    {
        return Path::join($this->absolutePublicDirectoryPath, $this->relativeMediaDirectoryPath, $relativeFilePath);
    }

    public function getRelativeFilePath(string $absoluteFilePath, string $additionnalBasePath = ''): string
    {
        return Path::makeRelative(
            $absoluteFilePath,
            Path::join($this->absolutePublicDirectoryPath, $this->relativeMediaDirectoryPath, $additionnalBasePath)
        );
    }

    public function getMediaManagerRelativeFilePath(string $relativeFilePath): string
    {
        return Path::join($this->relativeMediaDirectoryPath, $relativeFilePath);
    }
}
