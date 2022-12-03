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

final class FileTooBigException extends Exception
{
    private string $filename;

    private int $size;

    private int $maxAllowedSizeBytes;

    private string $maxAllowedSize;

    public function __construct(string $filename, int $size, int $maxAllowedSizeBytes, string $maxAllowedSize)
    {
        $this->filename = $filename;
        $this->size = $size;
        $this->maxAllowedSizeBytes = $maxAllowedSizeBytes;
        $this->maxAllowedSize = $maxAllowedSize;
        parent::__construct(
            sprintf('File `%s` couldn\'t be created. File size %s is bigger than %s bytes. (%s)',
            $filename,
            $size,
            $maxAllowedSizeBytes,
            $maxAllowedSize
        ));
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getMaxAllowedSizeBytes(): int
    {
        return $this->maxAllowedSizeBytes;
    }

    public function getMaxAllowedSize(): string
    {
        return $this->maxAllowedSize;
    }
}
