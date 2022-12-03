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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Exception;

use Exception;

final class InvalidMimeTypeException extends Exception
{
    private array $allowedMimeTypes;

    private string $mimeType;

    public function __construct(array $allowedMimeTypes, string $mimeType)
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->mimeType = $mimeType;

        parent::__construct(sprintf(
            'Invalid mime type. Excepted one of %s, `%s` given',
            implode(', ', $allowedMimeTypes),
            $mimeType
        ));
    }

    public function getAllowedMimeTypes(): array
    {
        return $this->allowedMimeTypes;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}
