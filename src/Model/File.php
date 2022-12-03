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

final class File implements FileInterface
{
    private string $name;

    private string $path;

    private string $fullPath;

    public function __construct(string $name, string $path, string $fullPath)
    {
        $this->name = $name;
        $this->path = $path;
        $this->fullPath = $fullPath;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function isDir(): bool
    {
        return is_dir($this->fullPath);
    }

    public function isFile(): bool
    {
        return is_file($this->fullPath);
    }
}
