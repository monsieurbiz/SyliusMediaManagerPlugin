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
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function findAllFromPath(
        string $absoluteDirectoryPath,
        bool $withParentLink = true,
        int $page = 1,
        int $itemsPerPage = 20,
    ): array {
        $elements = [];

        if (!is_dir($absoluteDirectoryPath)) {
            throw new CannotReadFolderException($absoluteDirectoryPath);
        }

        if (true === $withParentLink) {
            $elements[] = $this->fileFactory->createParentLinkFile($absoluteDirectoryPath);
        }

        $list = (new Finder())->in($absoluteDirectoryPath)->depth(0);
        $allFiles = [];
        foreach ($list as $file) {
            $allFiles[] = $this->fileFactory->createFromSplFileInfo($file);
        }

        // Put folders before files then sort by name case insensitive
        usort(
            $allFiles,
            static fn (FileInterface $fileA, FileInterface $fileB): int => (
                FileInterface::TYPE_FOLDER === $fileA->getType() ? 0 : 1
            ) <=> (FileInterface::TYPE_FOLDER === $fileB->getType() ? 0 : 1)
            ?: strcasecmp($fileA->getName(), $fileB->getName())
        );

        // Apply pagination
        $offset = ($page - 1) * $itemsPerPage;
        $paginatedFiles = \array_slice($allFiles, $offset, $itemsPerPage);

        return array_merge($elements, $paginatedFiles);
    }

    /**
     * @inheritDoc
     */
    public function countFromPath(string $absoluteDirectoryPath): int
    {
        if (!is_dir($absoluteDirectoryPath)) {
            throw new CannotReadFolderException($absoluteDirectoryPath);
        }

        $list = (new Finder())->in($absoluteDirectoryPath)->depth(0);

        return $list->count();
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
