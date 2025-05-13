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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Repository;

use MonsieurBiz\SyliusMediaManagerPlugin\Exception\CannotReadFolderException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotFoundException;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\FileInterface;

interface FileRepositoryInterface
{
    /**
     * @throws CannotReadFolderException
     *
     * @return array<FileInterface>
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function findAllFromPath(string $absoluteDirectoryPath, bool $withParentLink = true): array;

    /**
     * @throws FileNotFoundException
     */
    public function findOneFromPath(string $absoluteFilePath): FileInterface;
}
