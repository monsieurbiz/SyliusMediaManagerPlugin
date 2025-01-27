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

final class MimeTypesProvider implements MimeTypesProviderInterface
{
    public function getMimeTypesByType(?string $type): array
    {
        return match ($type) {
            'images' => ['image/*'],
            'audios' => ['audio/*'],
            'videos' => ['video/*'],
            'pdfs' => ['application/pdf'],
            default => ['image/*', 'audio/*', 'video/*', 'application/pdf'],
        };
    }
}
