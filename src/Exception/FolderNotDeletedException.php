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

final class FolderNotDeletedException extends Exception
{
    private string $folder;

    public function __construct(string $folder)
    {
        $this->folder = $folder;
        parent::__construct(\sprintf('Folder `%s` couldn\'t be deleted', $folder));
    }

    public function getFolder(): string
    {
        return $this->folder;
    }
}
