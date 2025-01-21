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

final class FileNotFoundException extends Exception
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
        parent::__construct(\sprintf('File not found `%s`', $path));
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
