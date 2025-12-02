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

interface FileInterface
{
    public const TYPE_FOLDER = 'folder';

    public const TYPE_IMAGE = 'image';

    public const TYPE_VIDEO = 'video';

    public const TYPE_PDF = 'pdf';

    public const TYPE_FAVICON = 'favicon';

    public const TYPE_AUDIO = 'audio';

    public const TYPE_FILE = 'file';

    public const FILE_TYPES = [
        self::TYPE_IMAGE,
        self::TYPE_VIDEO,
        self::TYPE_PDF,
        self::TYPE_FAVICON,
        self::TYPE_AUDIO,
        self::TYPE_FILE,
    ];

    public function getName(): string;

    public function setName(string $name): void;

    public function getType(): string;

    public function setType(string $type): void;

    public function getMimeType(): ?string;

    public function setMimeType(?string $mimeType): void;

    public function getLink(): ?string;

    public function setLink(?string $link): void;

    public function getPath(): ?string;

    public function setPath(?string $path): void;

    public function isDeletable(): bool;

    public function setDeletable(bool $deletable): void;

    public function isSelectable(): bool;

    public function setSelectable(bool $selectable): void;
}
