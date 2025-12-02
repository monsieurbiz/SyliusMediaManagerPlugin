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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Model;

use MonsieurBiz\SyliusMediaManagerPlugin\Provider\MimeTypesProviderInterface;

final class File implements FileInterface
{
    private string $name;

    private string $path;

    private string $fullPath;

    private string $mimeType;

    public function __construct(string $name, string $path, string $fullPath)
    {
        $this->name = $name;
        $this->path = $path;
        $this->fullPath = $fullPath;
        $this->mimeType = (string) mime_content_type($fullPath);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function isCurrentDir(): bool
    {
        return '.' === $this->name;
    }

    public function isParentDir(): bool
    {
        return '..' === $this->name;
    }

    public function isDir(): bool
    {
        return is_dir($this->fullPath);
    }

    public function isFile(): bool
    {
        return is_file($this->fullPath);
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function isImage(): bool
    {
        return \in_array($this->mimeType, MimeTypesProviderInterface::IMAGE_TYPE_MIMES, true);
    }

    public function isVideo(): bool
    {
        return \in_array($this->mimeType, MimeTypesProviderInterface::VIDEO_TYPE_MIMES, true);
    }

    public function isPdf(): bool
    {
        return \in_array($this->mimeType, MimeTypesProviderInterface::PDF_TYPE_MIMES, true);
    }

    public function isFavicon(): bool
    {
        return \in_array($this->mimeType, MimeTypesProviderInterface::FAVICON_TYPE_MIMES, true);
    }

    public function isAudio(): bool
    {
        return \in_array($this->mimeType, MimeTypesProviderInterface::AUDIO_TYPE_MIMES, true);
    }
}
