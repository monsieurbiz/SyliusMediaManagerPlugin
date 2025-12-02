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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Resolver;

use MonsieurBiz\SyliusMediaManagerPlugin\Provider\MimeTypesProviderInterface;
use Symfony\Component\Mime\MimeTypes;

readonly class FileMetadataResolver implements FileMetadataResolverInterface
{
    public function __construct(private MimeTypesProviderInterface $mimeTypesProvider)
    {
    }

    public function getMimeType(string $filePath): ?string
    {
        $mimeTypes = new MimeTypes();

        return $mimeTypes->guessMimeType($filePath);
    }

    public function getType(string $filePath): string
    {
        $mimeType = $this->getMimeType($filePath);

        return $this->mimeTypesProvider->getTypeByMimeType($mimeType);
    }
}
