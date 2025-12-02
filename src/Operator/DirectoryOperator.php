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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Operator;

use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotCreatedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FolderNotCreatedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FolderNotRenamedException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class DirectoryOperator implements DirectoryOperatorInterface
{
    public function __construct(
        private SluggerInterface $slugger,
        private Filesystem $filesystem,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addUploadedFile(string $directoryPath, UploadedFile $uploadedFile): void
    {
        // Build filename sluggified and lowered
        $extension = $uploadedFile->getClientOriginalExtension();
        $fileName = pathinfo($uploadedFile->getClientOriginalName(), \PATHINFO_FILENAME);
        $fileName = (string) $this->slugger->slug($fileName);
        $fileName = mb_strtolower($fileName, 'UTF-8');

        // Build final path and loop if the file already exists
        $fileBaseName = \sprintf('%s.%s', $fileName, $extension);
        $filePath = Path::join($directoryPath, $fileBaseName);
        $count = 1;
        while (file_exists($filePath)) {
            $fileBaseName = \sprintf('%s_%d.%s', $fileName, $count++, $extension);
            $filePath = Path::join($directoryPath, $fileBaseName);
        }

        if (empty($uploadedFile->getPathname())) {
            throw new FileNotCreatedException($fileBaseName, $uploadedFile->getErrorMessage());
        }

        $this->filesystem->dumpFile($filePath, $uploadedFile->getContent());
    }

    public function exists(string $directoryPath): bool
    {
        return $this->filesystem->exists($directoryPath);
    }

    public function createDir(string $directoryPath): void
    {
        if ($this->exists($directoryPath)) {
            return;
        }
        $this->filesystem->mkdir($directoryPath);
    }

    public function deleteFile(string $filePath): void
    {
        $this->filesystem->remove($filePath);
    }

    public function renameDirectory(string $directoryPath, string $newName): string
    {
        try {
            $parentPath = Path::getDirectory($directoryPath);
            $newDirectoryPath = Path::join($parentPath, $newName);
            $this->filesystem->rename($directoryPath, $newDirectoryPath);

            return $newDirectoryPath;
        } catch (IOException $e) {
            throw new FolderNotRenamedException($newName);
        }
    }

    public function createDirectory(string $directoryPath): void
    {
        try {
            $this->filesystem->mkdir($directoryPath);
        } catch (IOException $e) {
            throw new FolderNotCreatedException($directoryPath);
        }
    }
}
