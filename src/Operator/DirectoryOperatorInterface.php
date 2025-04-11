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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Operator;

use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotCreatedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface DirectoryOperatorInterface
{
    /**
     * @throws FileNotCreatedException
     */
    public function addUploadedFile(string $directoryPath, UploadedFile $uploadedFile): void;

    public function exists(string $directoryPath): bool;

    public function createDir(string $directoryPath): void;

    public function deleteFile(string $filePath): void;

    public function renameDirectory(string $directoryPath, string $newName): string;

    public function createDirectory(string $directoryPath): void;
}
