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

interface MimeTypesProviderInterface
{
    public const SVG_TYPE_MIMES = [
        'image/svg+xml',
        'image/svg',
        'image/svg-xml',
        'image/svg+xml;charset=utf-8',
        'image/svg+xml;charset=iso-8859-1',
    ];

    public const IMAGE_TYPE_MIMES = [
        'image/gif',
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/avif',
        ...self::SVG_TYPE_MIMES,
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

    public function getMimeTypesByType(?string $type): array;

    public function getTypeByMimeType(?string $mimeType): string;
}
