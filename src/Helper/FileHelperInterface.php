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

    public const IMAGE_TYPE_MIMES = [
        'image/gif',
        'image/jpeg',
        'image/png',
        'image/svg+xml',
        'image/webp',
        'image/avif',
    ];

    public const VIDEO_TYPE_MIMES = [
        'video/mp4',
        'video/mov',
        'video/3gp',
        'video/ogg',
        'video/webm',
    ];

    public const PDF_TYPE_MIMES = [
        'application/pdf',
    ];

    public const FAVICON_TYPE_MIMES = [
        'image/vnd.microsoft.icon',
        'image/x-icon',
        'image/ico',
        'image/svg+xml',
        'image/gif',
        'image/jpeg',
        'image/png',
    ];

    public const AUDIO_TYPE_MIMES = [
        'audio/mpeg',
        'audio/mpeg3',
        'audio/x-mpeg',
        'audio/x-mpeg-3',
    ];

    public function getMediaPath(): string;

    /**
     * @return FileInterface[]
     */
    public function list(string $path, ?string $folder = null): array;

    public function isValid(string $type, string $path, ?string $folder = null): bool;

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
