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
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotFoundException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileTooBigException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FolderNotCreatedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\InvalidMimeTypeException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\InvalidTypeException;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\File;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\String\Slugger\SluggerInterface;

final class FileHelper implements FileHelperInterface
{
    private string $mediaDirectory;

    private string $publicDirectory;

    private string $currentDirectory;

    private string $maxFileSize;

    private SluggerInterface $slugger;

    public function __construct(
        SluggerInterface $slugger,
        string $publicDirectory,
        string $mediaDirectory,
        string $maxFileSize
    ) {
        $this->slugger = $slugger;
        $this->publicDirectory = rtrim($publicDirectory, '/');
        $this->mediaDirectory = rtrim($publicDirectory, '/') . '/' . rtrim($mediaDirectory, '/');
        $this->currentDirectory = $this->mediaDirectory;
        $this->maxFileSize = $maxFileSize;
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
            $files[] = new File($fileName, $filePath, $this->getFullPath($filePath));
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
        switch ($type) {
            case FileHelperInterface::TYPE_IMAGE:
                if (!\in_array($mimeType, FileHelperInterface::IMAGE_TYPE_MIMES, true)) {
                    throw new InvalidMimeTypeException(FileHelperInterface::IMAGE_TYPE_MIMES, $mimeType);
                }

                break;
            case FileHelperInterface::TYPE_VIDEO:
                if (!\in_array($mimeType, FileHelperInterface::VIDEO_TYPE_MIMES, true)) {
                    throw new InvalidMimeTypeException(FileHelperInterface::VIDEO_TYPE_MIMES, $mimeType);
                }

                break;
            case FileHelperInterface::TYPE_PDF:
                if (!\in_array($mimeType, FileHelperInterface::PDF_TYPE_MIMES, true)) {
                    throw new InvalidMimeTypeException(FileHelperInterface::PDF_TYPE_MIMES, $mimeType);
                }

                break;
            case FileHelperInterface::TYPE_FAVICON:
                if (!\in_array($mimeType, FileHelperInterface::FAVICON_TYPE_MIMES, true)) {
                    throw new InvalidMimeTypeException(FileHelperInterface::FAVICON_TYPE_MIMES, $mimeType);
                }

                break;
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
        $finalName = sprintf('%s.%s', $fileName, $extension);
        $filePath = $this->getFullPath($path) . '/' . $finalName;
        $count = 1;
        while (file_exists($filePath)) {
            $finalName = sprintf('%s_%d.%s', $fileName, $count++, $extension);
            $filePath = $this->getFullPath($path) . '/' . $finalName;
        }

        // Check file size
        $maxAllowedSize = self::parseFilesize($this->maxFileSize);
        if ($file->getSize() > $maxAllowedSize) {
            throw new FileTooBigException($finalName, $file->getSize(), $maxAllowedSize, $this->maxFileSize);
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
     * Clean path to avoid server intrusions.
     */
    public function cleanPath(string $path): string
    {
        $path = trim($path, '.');
        $path = str_replace('/..', '', (string) $path);
        $path = str_replace('/.', '', (string) $path);
        $path = trim($path, '.');

        return trim($path, '/');
    }

    /**
     * Returns the given size from an ini value in bytes.
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    private static function parseFilesize(string $size): int
    {
        if ('' === $size) {
            return 0;
        }

        $size = strtolower($size);

        $max = ltrim($size, '+');
        if (str_starts_with($max, '0x')) {
            $max = \intval($max, 16);
        } elseif (str_starts_with($max, '0')) {
            $max = \intval($max, 8);
        } else {
            $max = (int) $max;
        }

        switch (substr($size, -1)) {
            case 't': $max *= 1024;
            // no break
            case 'g': $max *= 1024;
            // no break
            case 'm': $max *= 1024;
            // no break
            case 'k': $max *= 1024;
        }

        return $max;
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
