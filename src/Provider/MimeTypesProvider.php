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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Provider;

use MonsieurBiz\SyliusMediaManagerPlugin\Model\FileInterface;

final class MimeTypesProvider implements MimeTypesProviderInterface
{
    public function getMimeTypesByType(?string $type): array
    {
        return match ($type) {
            FileInterface::TYPE_IMAGE => self::IMAGE_TYPE_MIMES,
            FileInterface::TYPE_VIDEO => self::VIDEO_TYPE_MIMES,
            FileInterface::TYPE_PDF => self::PDF_TYPE_MIMES,
            FileInterface::TYPE_FAVICON => self::FAVICON_TYPE_MIMES,
            FileInterface::TYPE_AUDIO => self::AUDIO_TYPE_MIMES,
            default => array_unique(array_merge(self::IMAGE_TYPE_MIMES, self::VIDEO_TYPE_MIMES, self::PDF_TYPE_MIMES, self::FAVICON_TYPE_MIMES, self::AUDIO_TYPE_MIMES)),
        };
    }

    public function getTypeByMimeType(?string $mimeType): string
    {
        if (null === $mimeType) {
            return FileInterface::TYPE_FILE;
        }

        return match (true) {
            \in_array($mimeType, self::IMAGE_TYPE_MIMES, true) => FileInterface::TYPE_IMAGE,
            \in_array($mimeType, self::VIDEO_TYPE_MIMES, true) => FileInterface::TYPE_VIDEO,
            \in_array($mimeType, self::PDF_TYPE_MIMES, true) => FileInterface::TYPE_PDF,
            \in_array($mimeType, self::FAVICON_TYPE_MIMES, true) => FileInterface::TYPE_FAVICON,
            \in_array($mimeType, self::AUDIO_TYPE_MIMES, true) => FileInterface::TYPE_AUDIO,
            default => FileInterface::TYPE_FILE,
        };
    }
}
