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

use MonsieurBiz\SyliusMediaManagerPlugin\Helper\FileHelperInterface;

final class MimeTypesProvider implements MimeTypesProviderInterface
{
    public function getMimeTypesByType(?string $type): array
    {
        return match ($type) {
            FileHelperInterface::TYPE_IMAGE => self::IMAGE_TYPE_MIMES,
            FileHelperInterface::TYPE_VIDEO => self::VIDEO_TYPE_MIMES,
            FileHelperInterface::TYPE_PDF => self::PDF_TYPE_MIMES,
            FileHelperInterface::TYPE_FAVICON => self::FAVICON_TYPE_MIMES,
            FileHelperInterface::TYPE_AUDIO => self::AUDIO_TYPE_MIMES,
            default => array_unique(array_merge(self::IMAGE_TYPE_MIMES, self::VIDEO_TYPE_MIMES, self::PDF_TYPE_MIMES, self::FAVICON_TYPE_MIMES, self::AUDIO_TYPE_MIMES)),
        };
    }
}
