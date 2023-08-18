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

final class FileNotDeletedException extends Exception
{
    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
        parent::__construct(sprintf('File `%s` couldn\'t be deleted', $filename));
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
