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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Helper;

use MonsieurBiz\SyliusMediaManagerPlugin\Exception\CannotReadCurrentFolderException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\CannotReadFolderException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotCreatedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotDeletedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotFoundException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FolderNotCreatedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FolderNotDeletedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FolderNotRenamedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\InvalidMimeTypeException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\InvalidTypeException;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\File;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\FileInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Provider\MimeTypesProviderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class FileHelper implements FileHelperInterface
{
    private string $mediaDirectory;

    private string $publicDirectory;

    private string $currentDirectory;

    public function __construct(
        private SluggerInterface $slugger,
        string $publicDirectory,
        string $mediaDirectory,
        private MimeTypesProviderInterface $mimeTypesProvider,
    ) {
        $this->slugger = $slugger;
        $this->publicDirectory = rtrim($publicDirectory, '/');
        $this->mediaDirectory = rtrim($publicDirectory, '/') . '/' . rtrim($mediaDirectory, '/');
        $this->currentDirectory = $this->mediaDirectory;
    }

    public function getMediaPath(): string
    {
        return str_replace($this->publicDirectory, '', $this->mediaDirectory);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     *
     * @return FileInterface[]
     */
    public function list(string $path, ?string $folder = null): array
    {
        // Append the wanted folder from the root public media if necessary
        if (!empty($folder)) {
            $this->currentDirectory = $this->mediaDirectory . '/' . $this->cleanPath($folder);
        }
        $files = [];

        // Check the root gallery path exists
        if (!is_dir($this->currentDirectory)) {
            @mkdir($this->currentDirectory, 0777, true);
        }

        $fullPath = $this->getFullPath($path);

        // Check the source directory path exists
        if (!is_dir($fullPath)) {
            throw new CannotReadCurrentFolderException($fullPath);
        }

        // List files
        $fileNames = @scandir($fullPath);
        if (!$fileNames) {
            throw new CannotReadFolderException($path);
        }

        // Manage files given by the scan and build an array of File objects
        foreach ($fileNames as $fileName) {
            // Ignore current
            if ('.' === $fileName) {
                continue;
            }

            // Build parent folder path if authorized
            if ('..' === $fileName) {
                $parentPath = $this->getParentPath($path);
                // Don't allow to be upper than the current folder
                if (null === $parentPath) {
                    continue;
                }
                $files[] = new File($fileName, $parentPath, $this->getFullPath($parentPath));

                continue;
            }

            $cleanPath = $this->cleanPath($path);
            $filePath = (!empty($cleanPath) ? $cleanPath . '/' : '') . $fileName;

            // If the cleaned path generate a weird path, check the file still exists
            $fullFilePath = $this->getFullPath($filePath);
            if (!file_exists($fullFilePath)) {
                continue;
            }

            $files[] = new File($fileName, $filePath, $fullFilePath);
        }

        return $files;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function isValid(string $type, string $path, ?string $folder = null): bool
    {
        // Append the wanted folder from the root public media if necessary
        if (!empty($folder)) {
            $this->currentDirectory = $this->mediaDirectory . '/' . $this->cleanPath($folder);
        }

        // Check the given type from request is valid on server side
        if (!\in_array($type, FileHelperInterface::FILE_TYPES, true)) {
            throw new InvalidTypeException($type, FileHelperInterface::FILE_TYPES);
        }

        // Check the file exists after cleaning and get the full path
        $file = $this->getFullPath($path);
        if (!is_file($file)) {
            throw new FileNotFoundException($path);
        }

        // Check mime types of the file depending on the wanted type
        $mimeTypes = new MimeTypes();
        $mimeType = (string) $mimeTypes->guessMimeType($file);
        $allowedMimeTypes = $this->mimeTypesProvider->getMimeTypesByType($type);
        if (!\in_array($mimeType, $allowedMimeTypes, true)) {
            throw new InvalidMimeTypeException(MimeTypesProviderInterface::IMAGE_TYPE_MIMES, $mimeType);
        }

        return true;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function upload(UploadedFile $file, string $path, ?string $folder = null): string
    {
        // Append the wanted folder from the root public media if necessary
        if (!empty($folder)) {
            $this->currentDirectory = $this->mediaDirectory . '/' . $this->cleanPath($folder);
        }

        // Build filename sluggified and lowered
        $extension = $file->getClientOriginalExtension();
        $fileName = str_replace('.' . $extension, '', $file->getClientOriginalName());
        $fileName = (string) $this->slugger->slug($fileName);
        $fileName = mb_strtolower($fileName, 'UTF-8');

        // Build final path and loop if the file already exists
        $finalName = \sprintf('%s.%s', $fileName, $extension);
        $filePath = $this->getFullPath($path) . '/' . $finalName;
        $count = 1;
        while (file_exists($filePath)) {
            $finalName = \sprintf('%s_%d.%s', $fileName, $count++, $extension);
            $filePath = $this->getFullPath($path) . '/' . $finalName;
        }

        // File not uploaded or cannot be saved
        if (empty($file->getPathname()) || !@file_put_contents($filePath, $file->getContent())) {
            throw new FileNotCreatedException($finalName, $file->getErrorMessage());
        }

        return $finalName;
    }

    /**
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function createFolder(string $newFolder, string $path, ?string $folder = null): string
    {
        // Append the wanted folder from the root public media if necessary
        if (!empty($folder)) {
            $this->currentDirectory = $this->mediaDirectory . '/' . $this->cleanPath($folder);
        }

        $newFolder = (string) $this->slugger->slug($newFolder);
        $newFolder = mb_strtolower($newFolder, 'UTF-8');
        $filePath = $this->getFullPath($path) . '/' . $newFolder;

        if (!file_exists($filePath) && !@mkdir($filePath)) {
            throw new FolderNotCreatedException($newFolder);
        }

        return $newFolder;
    }

    /**
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function deleteFolder(string $path, ?string $folder = null): string
    {
        // Append the wanted folder from the root public media if necessary
        if (!empty($folder)) {
            $this->currentDirectory = $this->mediaDirectory . '/' . $this->cleanPath($folder);
        }

        $folderPath = $this->getFullPath($path);
        $parentPath = \dirname($folderPath);

        if (empty($path) || !file_exists($folderPath) || !@rmdir($folderPath)) {
            throw new FolderNotDeletedException($folderPath);
        }

        return $parentPath;
    }

    /**
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function deleteFile(string $path, ?string $folder = null): string
    {
        // Append the wanted folder from the root public media if necessary
        if (!empty($folder)) {
            $this->currentDirectory = $this->mediaDirectory . '/' . $this->cleanPath($folder);
        }

        $filePath = $this->getFullPath($path);
        $parentPath = \dirname($filePath);

        if (empty($path) || !file_exists($filePath) || !@unlink($filePath)) {
            throw new FileNotDeletedException($filePath);
        }

        return $parentPath;
    }

    /**
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function renameFolder(string $newFolderName, string $path, ?string $folder = null): string
    {
        // Append the wanted folder from the root public media if necessary
        if (!empty($folder)) {
            $this->currentDirectory = $this->mediaDirectory . '/' . $this->cleanPath($folder);
        }

        // We remove the last part of the path to get the parent path
        $arrayPath = explode('/', $path, -1);
        $arrayPath[] = $newFolderName;
        $newPath = implode('/', $arrayPath);

        $oldPath = $this->getFullPath($path);
        $newFolderName = (string) $this->slugger->slug($newFolderName);
        $newFolderName = mb_strtolower($newFolderName, 'UTF-8');
        $newFolderPath = $this->getFullPath($newPath);

        if (!@rename($oldPath, $newFolderPath)) {
            throw new FolderNotRenamedException($newFolderName);
        }

        return $newPath;
    }

    /**
     * Clean path to avoid server intrusions.
     */
    public function cleanPath(string $path): string
    {
        if (false === strpos($path, '/')) {
            return $path;
        }

        $path = trim($path, '.');
        $path = str_replace('/..', '', (string) $path);
        $path = str_replace('/.', '', (string) $path);
        $path = trim($path, '.');

        return trim($path, '/');
    }

    /**
     * Retrieve the full path to work with on server side.
     */
    private function getFullPath(string $path): string
    {
        return $this->currentDirectory . '/' . $this->cleanPath($path);
    }

    /**
     * Build the parent path.
     */
    private function getParentPath(string $path): ?string
    {
        $parentPath = \dirname($path);
        if (empty($parentPath)) {
            return null;
        }

        if ('.' === $parentPath) {
            $parentPath = '';
        }

        return $this->cleanPath($parentPath);
    }
}
