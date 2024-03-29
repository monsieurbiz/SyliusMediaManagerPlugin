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

use MonsieurBiz\SyliusMediaManagerPlugin\Helper\FileHelperInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\FileInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class FileExtension extends AbstractExtension
{
    private FileHelperInterface $fileHelper;

    public function __construct(FileHelperInterface $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_media_manager_file_path', [$this, 'getMediaManagerFilePath']),
            new TwigFunction('is_empty_files_list', [$this, 'isEmptyFilesList']),
        ];
    }

    public function getMediaManagerFilePath(string $path): string
    {
        return $this->fileHelper->getMediaPath() . '/' . $this->fileHelper->cleanPath($path);
    }

    public function isEmptyFilesList(array $files): bool
    {
        return !(bool) \count(array_filter($files, function (FileInterface $file) {
            return !$file->isCurrentDir() && !$file->isParentDir();
        }));
    }
}
