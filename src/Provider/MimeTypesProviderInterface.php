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

    public function getMimeTypesByType(?string $type): array;
}
