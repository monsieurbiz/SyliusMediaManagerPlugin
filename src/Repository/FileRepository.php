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
use MonsieurBiz\SyliusMediaManagerPlugin\Factory\FileFactoryInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\FileInterface;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

readonly class FileRepository implements FileRepositoryInterface
{
    public function __construct(
        private FileFactoryInterface $fileFactory,
    ) {
    }

    /**
     * @inheritDoc
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function findAllFromPath(string $absoluteDirectoryPath, bool $withParentLink = true): array
    {
        $elements = [];

        if (!is_dir($absoluteDirectoryPath)) {
            throw new CannotReadFolderException($absoluteDirectoryPath);
        }

        if (true === $withParentLink) {
            $elements[] = $this->fileFactory->createParentLinkFile($absoluteDirectoryPath);
        }

        $list = (new Finder())->in($absoluteDirectoryPath)->depth(0)->sortByName();
        foreach ($list as $file) {
            $elements[] = $this->fileFactory->createFromSplFileInfo($file);
        }

        return $elements;
    }

    /**
     * @inheritDoc
     */
    public function findOneFromPath(string $absoluteFilePath): FileInterface
    {
        if (!file_exists($absoluteFilePath)) {
            throw new FileNotFoundException($absoluteFilePath);
        }

        $splFileInfo = new SplFileInfo($absoluteFilePath);

        return $this->fileFactory->createFromSplFileInfo($splFileInfo);
    }
}
