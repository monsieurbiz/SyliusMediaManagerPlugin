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

use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotCreatedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileHelperInterface
{
    public const TYPE_IMAGE = 'image';

    public const TYPE_VIDEO = 'video';

    public const TYPE_PDF = 'pdf';

    public const TYPE_FILE = 'file';

    public const TYPE_FAVICON = 'favicon';

    public const TYPE_AUDIO = 'audio';

    public const FILE_TYPES = [
        self::TYPE_IMAGE,
        self::TYPE_VIDEO,
        self::TYPE_PDF,
        self::TYPE_FILE,
        self::TYPE_FAVICON,
        self::TYPE_AUDIO,
    ];

    public function getMediaPath(): string;

    /**
     * @return FileInterface[]
     */
    public function list(string $path, ?string $folder = null): array;

    public function isValid(string $type, string $path, ?string $folder = null): bool;

    /**
     * @throws FileNotCreatedException
     */
    public function upload(UploadedFile $file, string $path, ?string $folder = null): string;

    public function createFolder(string $newFolder, string $path, ?string $folder = null): string;

    public function deleteFolder(string $path, ?string $folder = null): string;

    public function deleteFile(string $path, ?string $folder = null): string;

    public function renameFolder(string $newFolderName, string $path, ?string $folder = null): string;

    /**
     * Clean path to avoid server intrusions.
     */
    public function cleanPath(string $path): string;
}
