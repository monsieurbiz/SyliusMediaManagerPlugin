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

use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotFoundException;
use MonsieurBiz\SyliusMediaManagerPlugin\Provider\MimeTypesProviderInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Repository\FileRepositoryInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Resolver\FilePathResolverInterface;
use Symfony\Component\Filesystem\Path;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class FileExtension extends AbstractExtension
{
    public function __construct(
        private readonly FilePathResolverInterface $filePathResolver,
        private readonly FileRepositoryInterface $fileRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_media_manager_file_path', [$this, 'getMediaManagerFilePath']),
            new TwigFunction('get_mime_type', [$this, 'getMimeType']),
            new TwigFunction('is_svg_image', [$this, 'isSvgImage']),
        ];
    }

    public function getMediaManagerFilePath(string $path): string
    {
        return '/' . $this->filePathResolver->getMediaManagerRelativeFilePath($path);
    }

    public function getMimeType(string $relativeFilePath): ?string
    {
        $absoluteFilePath = $this->filePathResolver->getAbsoluteFilePath($relativeFilePath);

        try {
            $file = $this->fileRepository->findOneFromPath($absoluteFilePath);

            return $file->getMimeType();
        } catch (FileNotFoundException) {
            // If the file is not found in the gallery, we try to get it from the image directory
            $absoluteFilePath = $this->filePathResolver->getAbsoluteFilePath(
                Path::join('image', $relativeFilePath)
            );

            try {
                $file = $this->fileRepository->findOneFromPath($absoluteFilePath);

                return $file->getMimeType();
            } catch (FileNotFoundException) {
                return null;
            }
        }
    }

    public function isSvgImage(string $relativeFilePath): bool
    {
        return \in_array($this->getMimeType($relativeFilePath), MimeTypesProviderInterface::SVG_TYPE_MIMES, true);
    }
}
