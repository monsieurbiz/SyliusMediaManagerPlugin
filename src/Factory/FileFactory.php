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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Factory;

use MonsieurBiz\SyliusMediaManagerPlugin\Model\File;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\FileInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Resolver\FileMetadataResolverInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Resolver\FilePathResolverInterface;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

readonly class FileFactory implements FileFactoryInterface
{
    public function __construct(
        private FileMetadataResolverInterface $fileMetadataResolver,
        private FilePathResolverInterface $filePathResolver,
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function createFromSplFileInfo(SplFileInfo $splFileInfo): FileInterface
    {
        $deletable = true;
        if (true === $splFileInfo->isDir()) {
            $deletable = 0 >= (new Finder())->in($splFileInfo->getRealPath())->files()->count();
        }

        $file = new File();
        $file->setName($splFileInfo->getFilename());
        $file->setType($splFileInfo->isDir() ? FileInterface::TYPE_FOLDER : $this->fileMetadataResolver->getType($splFileInfo->getRealPath()));
        $file->setMimeType($splFileInfo->isDir() ? null : $this->fileMetadataResolver->getMimeType($splFileInfo->getRealPath()));
        $file->setlink($splFileInfo->isDir() ? ($splFileInfo->getRealPath() ?: null) : null);
        $file->setPath($this->filePathResolver->getRelativeFilePath($splFileInfo->getRealPath()));
        $file->setDeletable($deletable);
        $file->setSelectable(!$splFileInfo->isDir());

        return $file;
    }

    public function createParentLinkFile(string $absoluteDirectoryPath): FileInterface
    {
        $file = new File();
        $file->setName('...');
        $file->setType(FileInterface::TYPE_FOLDER);
        $file->setLink(\dirname($absoluteDirectoryPath));

        return $file;
    }
}
